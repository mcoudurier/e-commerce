<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Order;
use App\Form\OrderStatusType;

class OrderController extends AbstractController
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

    public function show(Request $req, $id)
    {
        $order = $this->getDoctrine()
            ->getRepository(Order::class)
            ->find($id);

        if (!$order) {
            throw $this->createNotFoundException('Cette commande n\'existe pas');
        }

        $form = $this->createForm(OrderStatusType::class, $order);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();
        }

        return $this->render('admin/order_details.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }
}
