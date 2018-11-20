<?php
namespace App\Payments;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Transaction;
use App\Entity\Basket;

/**
 * Helps generating paypal transactions
 */
class PaypalFactory
{
    /**
     * Creates a paypal transaction from the basket
     *
     * @param Basket $basket
     * @return Transaction
     */
    public static function create(Basket $basket): Transaction
    {
        $products = $basket->getProducts();

        $itemList = new ItemList();

        foreach ($products as $product) {
            $item = (new Item())
                 ->setName($product->getName())
                 ->setCurrency('EUR')
                 ->setQuantity($product->getQuantity())
                 ->setPrice($product->getPrice());
            
            $itemList->addItem($item);
        }

        $subTotal = $basket->totalPrice($products);

        $details = (new Details())
            ->setShipping($basket->getShippingFee())
            //->setTax()
            ->setSubtotal($subTotal);

        $amount = (new Amount())
            ->setCurrency('EUR')
            ->setTotal($basket->grandTotal())
            ->setDetails($details);

        return (new Transaction())
            ->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());
    }
}
