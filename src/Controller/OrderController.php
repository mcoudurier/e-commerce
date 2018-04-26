<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Basket;

class OrderController extends Controller
{
    private $config;

    private $stripePk;

    private $basket;

    public function __construct(ObjectManager $objectManager)
    {
        $this->basket = new Basket($objectManager);
        $this->config = require(__DIR__ . '/../../config/stripe.php');
        $this->stripePk = $this->config['publishable_key'];
    }

    public function details()
    {
        $products = $this->basket->getProducts();
        $totalPrice = $this->basket->totalPrice($products) * 100;

        return $this->render('shop/order_details.html.twig', [
            'stripe_pk' => $this->stripePk,
            'total_price' => $totalPrice
        ]);
    }
}
