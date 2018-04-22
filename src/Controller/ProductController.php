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

        return $this->render('shop/index.html.twig', [
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

        return $this->render('shop/product_single.html.twig', [
            'product' => $product
        ]);
    }

    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();
       
        $product = $em->getRepository(Product::class)->find($id);
        $product->setActive(false);
        
        $em->persist($product);
        $em->flush();

        $this->addFlash('success', 'Produit supprimé');

        return $this->redirectToRoute('admin_index');
    }
}
