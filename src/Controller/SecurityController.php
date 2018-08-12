<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginUserType;
use App\Form\ProfileUserType;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/security", name="security")
     */
    public function index()
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/register", name="register")
     * Cette route renvoie l'utilisateur vers la page d'enregistreement, ensuite elle renvoie vers le login une fois l'enregistrement effectué.
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     * 
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $user = new User();
        $form = $this->createForm(LoginUserType::class, $user);

        return $this->render('security/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileUserType:: class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('security/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
