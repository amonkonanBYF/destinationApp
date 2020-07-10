<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\RegisterType;

class UserController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

        /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {

        if($this->getUser())
        {
            return $this->redirectToRoute('home');
        }

        $user = new User();

        $registerForm = $this->createForm(RegisterType::class, $user);

        $registerForm->handleRequest($request);

        $user->setRoles(["ROLE_USER"]);

        if($registerForm->isSubmitted() && $registerForm->isValid())
        {
            $encoded = $encoder->encodePassword($user ,$user->getPassword());

            $user->setPassword($encoded);

            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "Your account has been created");

            return $this->redirectToRoute('login');

        }
        return $this->render('user/register.html.twig', [
            'registerForm'=>$registerForm->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        if($this->getUser())
        {
            return $this->redirectToRoute('home');
        }
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('user/login.html.twig', [
            'error'=>$error
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){}

}
