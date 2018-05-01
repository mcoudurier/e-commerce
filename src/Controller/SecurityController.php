<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegisterType;
use App\Form\ChangePasswordType;
use App\Form\Model\ChangePassword;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends Controller
{
    public function login(Request $req, AuthenticationUtils $authenticationUtils, ?bool $order): Response
    {
        $user = new User();

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class, $user, [
            'action' => $this->generateUrl('security_login'),
        ]);
        
        return $this->render('shop/account/login_form.html.twig', [
            'loginForm' => $form->createView(),
            'error' => $error,
            'lastUserName' => $lastUsername,
            'order' => $order,
        ]);
    }

    public function register(UserPasswordEncoderInterface $passwordEncoder, ?bool $order): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $masterRequest = $this->get('request_stack')->getMasterRequest();
        $form->handleRequest($masterRequest);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setRoles('ROLE_USER');

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->render('shop/account/register_form.html.twig', [
            'registrationForm' => $form->createView(),
            'order' => $order,
        ]);
    }

    public function changePassword(Request $req, UserPasswordEncoderInterface $passwordEncoder): Response
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

        return $this->render('shop/account/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
