<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegisterType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    public function welcome()
    {
        return $this->render('User/welcome.html.twig');
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
        
        return $this->render('User/login.html.twig', [
            'loginForm' => $form->createView(),
            'error' => $error,
            'lastUserName' => $lastUsername
        ]);
    }

    public function register(Request $req)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user, [
            'action' => $this->generateUrl('user_register'),
            'method' => 'POST'
        ]);

        $form->handleRequest($req);
        
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors();
            return $this->redirectToRoute('user_welcome', [
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            /*
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            */

            return $this->redirectToRoute('index');
        }

        return $this->render('User/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
