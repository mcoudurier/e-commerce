<?php
namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Basket;
use App\Entity\Product;

class BasketTest extends TestCase
{
     /**
     * @runInSeparateProcess
     */
    public function testAdd()
    {
        $basket = new Basket();

        $product = new Product();
        $product->setId(1);

        $basket->add($product);
        $id = $product->getId();

        $this->assertEquals(true, $basket->hasProduct($id));
    }

     /**
     * @runInSeparateProcess
     */
    public function testAddTwoTimes()
    {
        $basket = new Basket();

        $product = new Product();
        $product->setId(1);

        $basket->add($product);
        $basket->add($product);
        $id = $product->getId();

        $this->assertEquals(2, $basket->getQuantity($product));
    }
     
    /**
     * @runInSeparateProcess
     */
    public function testUpdate()
    {
        $basket = new Basket();

        $product = new Product();
        $product->setId(1);

        $basket->add($product);
        
        $quantity = 5;
        $basket->update($product, $quantity);

        $this->assertEquals(5, $basket->getQuantity($product));
    }

    /**
     * @runInSeparateProcess
     */
    public function testRemove()
    {
        $basket = new Basket();

        $product = new Product();
        $product->setId(1);

        $basket->add($product);
        $basket->remove($product);
        
        $id = $product->getId();
        $this->assertEquals(false, $basket->hasProduct($id));
    }

    /**
     * @runInSeparateProcess
     */
    public function testCount()
    {
        $basket = new Basket();

        $product = new Product();
        $product->setId(1);
        
        $product1 = new Product();
        $product1->setId(2);

        $basket->add($product);
        $basket->add($product);
        $basket->add($product1);

        $this->assertEquals(3, $basket->count());
    }

    /**
     * @runInSeparateProcess
     */
    public function testTotalPrice()
    {
        $basket = new Basket();
        $products = [];

        $product = new Product();
        $product->setPrice(5.34);
        $product->setQuantity(2);
        $products[] = $product;
        
        $product1 = new Product();
        $product1->setPrice(125.23);
        $product1->setQuantity(1);
        $products[] = $product1;

        $this->assertEquals(135.91, $basket->totalPrice($products));
    }

    /**
     * @runInSeparateProcess
     */
    public function testHasProducts()
    {
        $basket = new Basket();
        $product = new Product();
        $product->setId(1);
        $basket->add($product);
        
        $this->assertEquals(true, $basket->hasProducts());

        $basket->clear();
        $this->assertEquals(false, $basket->hasProducts());
    }
}
