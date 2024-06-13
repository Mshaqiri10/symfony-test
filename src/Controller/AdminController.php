<?php

namespace App\Controller;

use App\Entity\Userat;
use App\Form\UserRoleType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em,)
    {
    }

    #[Route('/admin/users', name: 'admin_user_list')]
    public function index(Request $request): Response
    {
        // Fetch all users from the database using the entity manager
        $users = $this->em->getRepository(Userat::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/{id}/edit-role', name: 'admin_edit_user_role')]
    public function edit(Request $request, Userat $user): Response
    {
        // Fetch the entity manager from the injected dependency
        $entityManager = $this->em;

        // Create the edit form for the user
        $form = $this->createForm(UserRoleType::class, $user);

        // Handle the form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist changes to the database
            $entityManager->flush();

            // Redirect back to the user list
            return $this->redirectToRoute('admin_user_list');
        }

        // Render the edit user form
        return $this->render('admin/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
