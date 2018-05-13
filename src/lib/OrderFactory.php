<?php
namespace App\lib;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Basket;
use App\Entity\Transaction;
use App\Entity\User;

class OrderFactory
{
    public static function create(Basket $basket, User $user, string $paymentMethod)
    {
        $order = new Order();
        
        foreach ($basket->getProducts() as $product) {
            $order->addProduct(new OrderProduct($product));
        }
        
        $address = $user->getAddresses()[0];
        $totalPrice = $basket->grandTotal();

        $order->setUser($user)
              ->setShippingAddress($address)
              ->setBillingAddress($address)
              ->setStatus('processing')
              ->setShippingMethod($basket->getShippingMethod())
              ->setTransaction(
                  new \App\Entity\Transaction(
                    $paymentMethod, $totalPrice)
              );

        return $order;
    }
}
