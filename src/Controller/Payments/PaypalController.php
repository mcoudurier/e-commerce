<?php
namespace App\Controller\Payments;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Common\Persistence\ObjectManager;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\PaymentExecution;
use App\Entity\Basket;
use App\Payments\PaypalFactory;
use App\Service\Mailer;
use App\Service\OrderFactory;

class PaypalController extends AbstractController
{
    private $basket;

    private $config;

    private $apiContext;

    private $session;

    public function __construct(ObjectManager $objectManager)
    {
        $this->basket = new Basket($objectManager);
        // TODO: Load the config in a cleaner way
        $this->config = require(__DIR__ . '/../../../config/paypal.php');
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->config['client_id'],
                $this->config['secret']
            )
        );
        $this->session = new Session();
    }

    /**
     * Generates the payment and redirects to the paypal checkout page
     *
     * @param Request $req
     * @return Response
     */
    public function paypalCheckout(Request $req)
    {
        if (!$this->session->get('checkout/payment')) {
            return $this->redirectToRoute('basket_show');
        }

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $baseUrl = $req->getScheme() . '://' . $req->getHttpHost();

        $redirectUrls = (new RedirectUrls())
            ->setReturnUrl($baseUrl . $this->generateUrl('paypal_payment'))
            ->setCancelUrl($baseUrl . $this->generateUrl('basket_show'));

        $payment = (new Payment())
            ->setPayer((new Payer())->setPaymentMethod('paypal'))
            ->setIntent('sale')
            ->addTransaction(PaypalFactory::create($this->basket))
            ->setRedirectUrls($redirectUrls);
        
        try {
            $payment->create($this->apiContext);
        } catch (\Exception $e) {
            return new Response('Payement impossible');
        }
        
        $this->session->set('checkout/paypal-checkout', true);
        
        return $this->redirect($payment->getApprovalLink());
    }

    /**
     * Actually executes the payment after the customer was redirected back from paypal
     *
     * @param Request $req
     * @param Mailer $mailer
     * @param OrderFactory $orderFactory
     * @return Response
     */
    public function paypalPayment(Request $req, Mailer $mailer, OrderFactory $orderFactory)
    {
        if (!$this->session->get('checkout/paypal-checkout')) {
            return $this->redirectToRoute('basket_show');
        }

        $payment = Payment::get($req->get('paymentId'), $this->apiContext);
        
        $execution = (new PaymentExecution())
            ->setPayerId($req->get('PayerID'))
            ->setTransactions($payment->getTransactions());
        
        try {
            $payment->execute($execution, $this->apiContext);
        } catch (\Exception $e) {
            return new Response('Payement impossible');
        }

        $user = $this->getUser();
        
        $order = $orderFactory->create($this->basket, $user, 'paypal');
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        $mailer->orderConfirmation($user);
        
        $this->basket->clear();

        return $this->render('shop/order_confirmation.html.twig');
    }
}
