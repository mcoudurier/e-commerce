<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Basket;
use App\Entity\Product;

class BasketController extends Controller
{
    private $basket;

    public function __construct()
    {
        $this->basket = new Basket();
    }

    public function show(Request $req)
    {
        $ids = $this->basket->getIds();
        $products = [];

        if ($ids)
        {
            $products = $this->getDoctrine()
                ->getRepository(Product::class)
                ->findAllById($ids);

            $products = $this->basket->setQuantities($products);
        }

        return $this->render('Basket/basket.html.twig', [
            'products' => $products
        ]);
    }

    public function add(Request $req)
    {
        $id = $req->get('id');
        
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);

        $this->basket->add($product);

        return $this->redirect('/product/' . $id);
    }

    public function remove(Request $req)
    {
        $id = $req->get('id');
        
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);

        $this->basket->remove($product);

        return $this->redirect('/basket');
    }

    public function update(Request $req)
    {

    }
}
