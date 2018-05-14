<?php
namespace App\Controller\Payments;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Basket;
use App\Service\Mailer;
use App\Service\OrderFactory;

class StripeController extends Controller
{
    private $basket;

    private $config;

    private $secretKey;

    public function __construct(ObjectManager $objectManager)
    {
        $this->basket = new Basket($objectManager);
        // TODO: Load the config in a cleaner way
        $this->config = require(__DIR__ . '/../../../config/stripe.php');
        $this->secretKey = $this->config['secret_key'];
    }

    public function stripeCheckout(Request $req, Mailer $mailer, OrderFactory $orderFactory)
    {
        \Stripe\Stripe::setApiKey($this->secretKey);

        $token = $req->get('stripeToken');
       
        // Stripe expects prices in pennies
        $totalPrice = $this->basket->grandTotal() * 100;

        $charge = \Stripe\Charge::create([
            'amount' => $totalPrice,
            'currency' => 'eur',
            'description' => 'Test charge',
            'source' => $token,
            'receipt_email' => $this->getUser()->getEmail(),
        ]);
        
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
