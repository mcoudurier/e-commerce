<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Product;
use App\Entity\Transaction;

class AdminController extends AbstractController
{
    public function index()
    {
        $productRows = $this->getDoctrine()
            ->getRepository(Product::class)
            ->countCurrentlySelling();
        
        $totalRevenue = $this->getDoctrine()
            ->getRepository(Transaction::class)
            ->sumAll();

        return $this->render('admin/index.html.twig', [
            'product_rows' => $productRows,
            'total_revenue' => $totalRevenue,
        ]);
    }
}
