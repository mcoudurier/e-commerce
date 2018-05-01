<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use App\Entity\Order;
use App\Entity\Transaction;

class OrderController extends Controller
{
    public function index()
    {
        $orders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->findAll();
        
        return $this->render('admin/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    public function show($id)
    {
        $order = $this->getDoctrine()
            ->getRepository(Order::class)
            ->find($id);

        if (!$order) {
            throw $this->createNotFoundException('Cette commande n\'existe pas');
        }

        return $this->render('admin/order_details.html.twig', [
            'order' => $order,
        ]);
    }
}
