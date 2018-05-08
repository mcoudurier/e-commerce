<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Address;
use App\Form\AddressType;
use App\Form\UserContactType;
use App\Repository\OrderRepository;

class UserController extends Controller
{
    public function welcome(Request $req, ?bool $order = null): Response
    {
        $order = $req->get('order');

        return $this->render('shop/account/welcome.html.twig', [
            'order' => $order,
        ]);
    }

    public function account(Request $req): Response
    {
        return $this->render('shop/account/account.html.twig');
    }

    public function editAddress(): Response
    {
        $address = new Address();
        
        $user = $this->getUser();
        if (!$user->getAddresses()->isEmpty()) {
            $address = $user->getAddresses()[0];
        } else {
            $user->addAddress($address);
        }
        
        $form = $this->createForm(UserContactType::class, $user);
        
        $masterRequest = $this->get('request_stack')->getMasterRequest();
        $form->handleRequest($masterRequest);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $user = $form->getData();
            
            $address->setUser($user)
                    ->setCountry('France')
                    ->setType('billing');

            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();
        }

        return $this->render('shop/account/user_contact_form.html.twig', [
            'address_form' => $form->createView(),
        ]);
    }

    public function orders(): Response
    {
        $orders = $this->getUser()->getOrders();

        return $this->render('shop/account/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    public function order(int $id, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository
            ->findOneByIdAndUser($id, $this->getUser()->getId());

        if (!$order) {
            throw $this->createNotFoundException();
        }

        return $this->render('shop/account/order_single.html.twig', [
            'order' => $order,
        ]);
    }
}
