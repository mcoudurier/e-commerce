<?php
namespace App\Controller\Payments;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Basket;
use App\Service\Mailer;
use App\Service\OrderFactory;

class StripeController extends AbstractController
{
    private $basket;

    private $config;

    private $secretKey;

    private $session;

    public function __construct(ObjectManager $objectManager)
    {
        $this->basket = new Basket($objectManager);
        // TODO: Load the config in a cleaner way
        $this->config = require(__DIR__ . '/../../../config/stripe.php');
        $this->secretKey = $this->config['secret_key'];
        $this->session = new Session();
    }

    public function stripeCheckout(Request $req, Mailer $mailer, OrderFactory $orderFactory): Response
    {
        if (!$this->session->get('checkout/payment')) {
            return $this->redirectToRoute('basket_show');
        }

        \Stripe\Stripe::setApiKey($this->secretKey);

        $token = $req->get('stripeToken');
       
        // Stripe expects prices in pennies
        $totalPrice = $this->basket->grandTotal() * 100;

        try {
            \Stripe\Charge::create([
                'amount' => $totalPrice,
                'currency' => 'eur',
                'description' => 'Test charge',
                'source' => $token,
                'receipt_email' => $this->getUser()->getEmail(),
            ]);
        } catch (\Stripe\Error\Card $e) {
            $this->addFlash('danger', 'Paiement refusé');
            return $this->redirectToRoute('checkout_payment');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Paiement impossible');
            return $this->redirectToRoute('checkout_payment');
        }
        
        $user = $this->getUser();
        
        $order = $orderFactory->create($this->basket, $user, 'stripe');

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();
        
        $mailer->orderConfirmation($user);

        $this->basket->clear();
        
        return $this->render('shop/order_confirmation.html.twig');
    }
}
