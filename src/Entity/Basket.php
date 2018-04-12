<?php
namespace App\Entity;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Session\Session;

class Basket implements \Countable
{
    public function __construct()
    {
        $this->session = new Session();
    }

    public function add(Product $product)
    {
        $products = $this->getProducts();
        $id = $product->getId();
        $quantity = 1;

        if ($this->hasProduct($id))
        {
            $quantity = $products[$product->getId()]['quantity'];
            $quantity++;
        }

        $products[$product->getId()] = [
            'id' => $id,
            'quantity' => $quantity
        ];

        $this->session->set('products', $products);
    }

    public function getIds(): ?array
    {
        if ($this->haveProducts())
        {
            $ids = [];

            foreach ($this->getProducts() as $product)
            {
                $ids[] = $product['id'];
            }

            return $ids;
        }

        return null;
    }

    public function getQuantity(Product $product)
    {
        return $this->getProducts()[$product->getId()]['quantity'];
    }

    public function setQuantities(array $products)
    {
        $setProducts = [];

        foreach ($products as $product)
        {
            $product->setQuantity($this->getQuantity($product));
            $setProducts[] = $product;
        }

        return $setProducts;
    }

    public function update()
    {

    }

    public function hasProduct(string $key): bool
    {
        return isset($this->getProducts()[$key]);
    }

    public function remove(Product $product)
    {
        $products = $this->getProducts();
        unset($products[$product->getId()]);
        $this->session->set('products', $products);
    }

    public function getProducts()
    {
        return $this->session->get('products');
    }

    public function haveProducts(): bool
    {
        return !empty($this->session->get('products'));
    }

    public function count(): int
    {
        $quantity = 0;

        if ($products = $this->getProducts())
        {
            foreach ($products as $product)
            {
                $quantity += $product['quantity'];
            }
        }

        return $quantity;
    }
}
