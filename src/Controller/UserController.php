<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    /**
     * @Route("/user/{id}", name="user_id")
     */
    public function index(Request $request, UserRepository $userRepository, int $id )
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $users = $userRepository->findAll();


        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }
}
