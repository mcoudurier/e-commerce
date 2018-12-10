<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Product;
use App\Repository\ProductRepository;

class ProductController extends AbstractController
{
    public function index(ProductRepository $productRepository)
    {
        $allProducts = $productRepository->findAll();
        $latestProducts = $productRepository->findLatest(3);

        return $this->render('shop/index.html.twig', [
            'all_products' => $allProducts,
            'latest_products' => $latestProducts,
        ]);
    }

    public function show($slug)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findOneBySlug($slug);

        if (!$product) {
            throw $this->createNotFoundException();
        }

        return $this->render('shop/product_single.html.twig', [
            'product' => $product,
        ]);
    }
}
