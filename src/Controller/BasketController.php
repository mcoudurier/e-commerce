<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Basket;
use App\Entity\Product;

class BasketController extends AbstractController
{
    private $basket;

    public function __construct(EntityManagerInterface $objectManager)
    {
        $this->basket = new Basket($objectManager);
    }

    public function show()
    {
        $products = [];
        $totalPrice = 0;

        if ($this->basket->hasProducts()) {
            $products = $this->basket->getProducts();
            $totalPrice = $this->basket->totalPrice($products);
        }

        return $this->render('shop/basket.html.twig', [
            'products' => $products,
            'totalPrice' => $totalPrice,
        ]);
    }

    public function add($id)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        
        if (!$product) {
            throw $this->createNotFoundException();
        }

        if ($product->hasStock()) {
            $this->basket->add($product);
        } else {
            $this->addFlash('primary', 'Le produit n\'est plus en stock');
        }

        $slug = $product->getSlug();

        return $this->redirectToRoute('product_show', [
            'slug' => $slug,
        ]);
    }

    public function remove($id)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        
        if (!$product) {
            throw $this->createNotFoundException();
        }

        $this->basket->remove($product);

        return $this->redirectToRoute('basket_show');
    }

    public function update(Request $req)
    {
        $data = json_decode($req->getContent(), true);
        $id = (int) $data['id'];
        $quantity = (int) $data['quantity'];
       
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        
        if (!$product) {
            throw $this->createNotFoundException();
        }

        $this->basket->update($product, $quantity);
            
        $products = $this->basket->getProducts();
        $totalPrice = $this->basket->totalPrice($products);

        return new JsonResponse([
            'price' => $product->calcTotalPrice(),
            'totalPrice' => $totalPrice,
        ]);
    }

    public function productCount()
    {
        return new Response(count($this->basket));
    }
}
