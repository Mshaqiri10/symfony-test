<?php

namespace App\Controller;

use App\Entity\Listing;
use App\Entity\User;
use App\Form\ListingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

class ListingController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em,private Security $sc)
    {
        
    }

    #[Route('/listings', name: 'app_listing')]
    public function index(): Response
    {
        // Get the currently logged-in user
        $user = $this->getUser();
    
        // If the user is not logged in, handle this case as needed
        if (!$user instanceof User) {
            // Handle case when user is not logged in
            // For example, redirect to login page or display an error message
        }
    
        // Fetch listings associated with the logged-in user
        $listings = $user->getListings();
    
        return $this->render('listing/index.html.twig', [
            'listings' => $listings,
        ]);
    }

    
    #[Route('/listing/new', name: 'listing_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $listing = new Listing();
        // $user = new User;
        $user = $this->getUser(); // Get the currently authenticated user

        $form = $this->createForm(ListingType::class, $listing, [
            'user_id' => $listing->getUser(), // Pass the user ID to the form
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the user on the listing
            $listing->setUser($user);

            $this->em->persist($listing);
            $this->em->flush();

            return $this->redirectToRoute('app_listing');
        }

        return $this->render('listing/new.html.twig', [
            'listing' => $listing,
            'form' => $form->createView(),
        ]);
    }
}
