<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Basket;
use App\Form\AddressType;

class CheckoutController extends Controller
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

    public function address(Request $req)
    {
        $address = $this->getUser()->getAddresses()[0];
        $form = $this->createForm(AddressType::class, $address);
        
        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $address->setType('shipping');

            return $this->redirectToRoute('checkout_shipping');
        }

        return $this->render('shop/checkout/address.html.twig', [
            'address_form' => $form->createView(),
        ]);
    }

    public function shipping(Request $req)
    {
        $form = $this->createFormBuilder()
            ->add('shipping', ChoiceType::class, [
                'expanded' => true,
                'choices' => [
                    'Colissimo' => 'colissimo',
                    'Chronopost' => 'chronopost',
                ],
            ])
            ->getForm();

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('checkout_summary');
        }

        return $this->render('shop/checkout/shipping.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function summary()
    {
        $products = $this->basket->getProducts();
        $totalPrice = $this->basket->totalPrice($products);
        $vatPrice = $this->basket->vatPrice($totalPrice);
        
        return $this->render('shop/checkout/summary.html.twig', [
            'products' => $products,
            'totalPrice' => $totalPrice,
            'vatPrice' => $vatPrice,
        ]);
    }

    public function payment()
    {
        $products = $this->basket->getProducts();
        $totalPrice = $this->basket->totalPrice($products) * 100;

        return $this->render('shop/checkout/payment.html.twig', [
            'stripe_pk' => $this->stripePk,
            'total_price' => $totalPrice,
        ]);
    }
}
