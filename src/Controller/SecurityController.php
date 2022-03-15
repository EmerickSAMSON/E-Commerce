<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher)
    {
        $user = new Users();

        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(

                $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                    )
                );

            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());
            // dd($user);
                
            $manager->persist($user);
            $manager->flush();
        }

        return $this->render('security/register.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
