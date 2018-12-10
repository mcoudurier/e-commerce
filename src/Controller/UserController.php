<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\UserContactType;
use App\Repository\OrderRepository;
use App\Repository\AddressRepository;

class UserController extends AbstractController
{
    public function welcome(Request $req): Response
    {
        $order = $req->get('order');

        return $this->render('shop/account/welcome.html.twig', [
            'order' => $order,
        ]);
    }

    public function account(): Response
    {
        return $this->render('shop/account/account.html.twig');
    }

    public function editAddress(AddressRepository $addressRepository): Response
    {
        $user = $this->getUser();
        
        $address = $addressRepository->findCurrentWithType($user->getId(), 'billing');

        $userContact = new \App\Form\Model\UserContact($user, $address);
        
        $form = $this->createForm(UserContactType::class, $userContact);
        
        $masterRequest = $this->get('request_stack')->getMasterRequest();
        $form->handleRequest($masterRequest);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $userContact = $form->getData();
            $address = $userContact->getAddress();
            
            $uow = $this->getDoctrine()
                ->getManager()
                ->getUnitOfWork();
            $uow->computeChangeSets();

            if ($uow->isEntityScheduled($address)) {
                $address = clone $address;
                $address->setDateCreated(new \DateTime());
            }

            $user->setFirstName($userContact->getFirstName())
                 ->setLastName($userContact->getLastName());

            $address->setUser($user)
                    ->setCountry('France')
                    ->setType('billing');
            
            if ($uow->isEntityScheduled($address)) {
                $address = clone $address;
                $address->setDateCreated(new \DateTime());
            }

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
