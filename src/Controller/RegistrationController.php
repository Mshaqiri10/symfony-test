<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\Validator\Validator\ValidatorInterface;


class RegistrationController extends AbstractController
{
    

    public function __construct(private EntityManagerInterface $entityManager)
    {
        
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            // Encode the plain password and set it to the user entity
            $hashedPassword = $userPasswordHasher->hashPassword(
                $user,
                $plainPassword
            );
            $user->setPassword($hashedPassword);
           // dd($form->getData($user));
            // Assign default roles (e.g., ROLE_USER)
            $defaultRole = 'ROLE_USER';
            $user->setRoles([$defaultRole]);
            // Persist and flush the user entity
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('all_listings');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
