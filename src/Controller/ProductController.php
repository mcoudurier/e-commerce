<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('home.html.twig', [
            'products' => $products
        ]);
    }

    public function show($id)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);

        if (!$product) {
            throw $this->createNotFoundException();
        }

        return $this->render('Product/single.html.twig', [
            'product' => $product
        ]);
    }
}
