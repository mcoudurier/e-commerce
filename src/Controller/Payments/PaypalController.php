<?php
namespace App\Controller\Payments;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Basket;
use Doctrine\Common\Persistence\ObjectManager;
use App\Payments\PaypalFactory;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\PaymentExecution;
use App\Entity\Order;

class PaypalController extends Controller
{
    private $basket;

    private $config;

    private $apiContext;

    public function __construct(ObjectManager $objectManager)
    {
        $this->basket = new Basket($objectManager);
        // TODO: Load the config in a cleaner way
        $this->config = require(__DIR__ . '/../../../config/paypal.php');
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->config['client_id'],
                $this->config['secret'])
        );
    }

    /**
     * Generates the payment and redirects to the paypal checkout page
     *
     * @param Request $req
     * @return void
     */
    public function paypalCheckout(Request $req)
    {
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
        
        return $this->redirect($payment->getApprovalLink());
    }

    /**
     * Actually executes the payment after the customer was redirected back from paypal
     *
     * @param Request $req
     * @return void
     */
    public function paypalPayment(Request $req, \Swift_Mailer $mailer)
    {
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
        $address = $user->getAddresses()[0];

        $order = new Order();
        $order->create($this->basket);
        $order->setUser($user)
              ->setShippingAddress($address)
              ->setBillingAddress($address)
              ->setStatus('processing')
              ->setTransaction(
                  new \App\Entity\Transaction(
                      'paypal', $this->basket->totalPrice($this->basket->getProducts()))
              );
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        $message = (new \Swift_Message('Confirmation de commande'))
            ->setFrom('send@example.com')
            ->setTo($this->getUser()->getEmail())
            ->setBody(
                $this->renderView('emails/order_confirmation.html.twig'),
                'text/html'
            );

        $mailer->send($message);

        return $this->render('shop/order_confirmation.html.twig');
    }
}
