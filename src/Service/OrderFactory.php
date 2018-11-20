<?php
namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Basket;
use App\Entity\User;
use App\Repository\AddressRepository;

class OrderFactory
{
    private $addressRepository;

    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }
    public function create(Basket $basket, User $user, string $paymentMethod)
    {
        $order = new Order();
        
        foreach ($basket->getProducts() as $product) {
            $order->addProduct(new OrderProduct($product));
        }

        $shippingAddress = $this->addressRepository->findCurrentWithType($user->getId(), 'shipping');
        $billingAddress = $this->addressRepository->findCurrentWithType($user->getId(), 'billing');
        
        $totalPrice = $basket->grandTotal();

        $order->setUser($user)
              ->setShippingAddress($shippingAddress)
              ->setBillingAddress($billingAddress)
              ->setStatus('processing')
              ->setShippingMethod($basket->getShippingMethod())
              ->setTransaction(
                  new \App\Entity\Transaction(
                      $paymentMethod,
                      $totalPrice
                  )
              );

        return $order;
    }
}
