<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\Security;

class PageController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(
        AuthenticationUtils $authenticationUtils
    ): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'action' => $this->generateUrl('app_register'),
        ]);

        return $this->render('home/index.html.twig', [
            'last_username'     => $lastUsername,
            'error'             => $error,
            'registrationForm'  => $form->createView()
        ]);

    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Security $security)
    {   
        $response = $security->logout();
        return $this->redirectToRoute('app_home');
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
        Security $security
    )
    {   
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'action' => $this->generateUrl('app_register'),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            $security->login($user);
            return $this->redirectToRoute('app_home');
        }
        return $this->redirectToRoute('app_home');
    }    

}
