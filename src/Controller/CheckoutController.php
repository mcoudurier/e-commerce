<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Basket;
use App\Form\AddressType;
use App\Entity\ShippingMethod;
use App\Repository\AddressRepository;

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

    public function address(Request $req, AddressRepository $addressRepository)
    {
        $address = $addressRepository
            ->findCurrentWithType($this->getUser()->getId(), 'shipping');
        $form = $this->createForm(AddressType::class, $address);
        
        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->getData();
           
            $uow = $this->getDoctrine()
                ->getManager()
                ->getUnitOfWork();
            $uow->computeChangeSets();

            if ($uow->isEntityScheduled($address)) {
                $address = clone $address;
                $address->setDateCreated(new \DateTime());
            }

            $address->setType('shipping')
                    ->setCountry('France')
                    ->setUser($this->getUser());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();

            return $this->redirectToRoute('checkout_shipping');
        }

        return $this->render('shop/checkout/address.html.twig', [
            'address_form' => $form->createView(),
        ]);
    }

    public function shipping(Request $req)
    {
        $shippingMethod = new ShippingMethod();

        $form = $this->createForm(\App\Form\ShippingMethodType::class, null);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $shippingMethod = $form->getData()['shippingMethod'];
            
            $this->basket->addShippingMethod($shippingMethod);
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
        $vatPrice = $this->basket->vatPrice($this->basket->grandTotal());
        $shippingFee = $this->basket->getShippingMethod()->getFee();
        $grandTotal = $this->basket->grandTotal();
        
        return $this->render('shop/checkout/summary.html.twig', [
            'products' => $products,
            'total_price' => $totalPrice,
            'shipping_fee' => $shippingFee,
            'vat_price' => $vatPrice,
            'grand_total' => $grandTotal,
        ]);
    }

    public function payment()
    {
        $products = $this->basket->getProducts();
        $totalPrice = $this->basket->grandTotal() * 100;

        return $this->render('shop/checkout/payment.html.twig', [
            'stripe_pk' => $this->stripePk,
            'total_price' => $totalPrice,
        ]);
    }
}
