<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\User;
use App\Entity\Address;
use App\Form\AddressType;
use App\Form\LoginType;
use App\Form\RegisterType;
use App\Form\ChangePasswordType;
use App\Form\Model\ChangePassword;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
    public function welcome($form = null)
    {
        return $this->render('shop/account/welcome.html.twig', [
            'form' => $form
        ]);
    }

    public function login(Request $req, AuthenticationUtils $authenticationUtils)
    {
        $user = new User();

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class, $user, [
            'action' => $this->generateUrl('user_login'),
            'method' => 'POST'
        ]);
        
        return $this->render('shop/account/login.html.twig', [
            'loginForm' => $form->createView(),
            'error' => $error,
            'lastUserName' => $lastUsername
        ]);
    }

    public function register($form = null, Request $req, UserPasswordEncoderInterface $passwordEncoder)
    {
        // Injected form with errors
        if ($form)
        {
            return $this->render('shop/account/register.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user, [
            'action' => $this->generateUrl('user_register'),
            'method' => 'POST'
        ]);

        $form->handleRequest($req);
        
        // Reinject in the welcome page to display errors
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->forward('App\Controller\UserController::welcome', [
                'form' => $form
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setRoles('ROLE_USER');

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_account');
        }

        return $this->render('shop/account/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    public function account(Request $req)
    {
        return $this->render('shop/account/account.html.twig');
    }

    public function editAddress(Request $req)
    {
        $address = new Address();
        
        $user = $this->getUser();
        if (!$user->getAddresses()->isEmpty())
        {
            $address = $user->getAddresses()[0];
        }
        
        $form = $this->createForm(AddressType::class, $address);
        
        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $address->setUser($user)
                    ->setCountry('France');

            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();
        }

        return $this->render('shop/account/editAddress.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function changePassword(Request $req, UserPasswordEncoderInterface $passwordEncoder)
    {
        $changePassword = new changePassword();

        $form = $this->createForm(changePasswordType::class, $changePassword);

        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            
            $newPassword = $passwordEncoder->encodePassword($user, $changePassword->getNewPassword());
            
            $user->setPassword($newPassword);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Mot de passe changé');

            return $this->redirectToRoute('user_account');
        }

        return $this->render('shop/account/changePassword.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
