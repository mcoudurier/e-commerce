<?php
namespace App\Controller\Payments;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Basket;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Order;

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

    public function stripeCheckout(Request $req, \Swift_Mailer $mailer)
    {
        \Stripe\Stripe::setApiKey($this->secretKey);

        $token = $req->get('stripeToken');
        
        $products = $this->basket->getProducts();
        $totalPrice = $this->basket->totalPrice($products) * 100;

        $charge = \Stripe\Charge::create([
            'amount' => $totalPrice,
            'currency' => 'eur',
            'description' => 'Test charge',
            'source' => $token,
            'receipt_email' => $this->getUser()->getEmail(),
        ]);
        
        $user = $this->getUser();
        $address = $user->getAddresses()[0];

        $order = new Order();
        $order->create($this->basket);
        $order->setUser($user)
              ->setShippingAddress($address)
              ->setBillingAddress($address);
        
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
