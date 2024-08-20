<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Listing;
use App\Form\ListingType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListingController extends AbstractController
{
    public function __construct( private EntityManagerInterface $em, private Security $sc) {
    }
    
    #[Route('/listings/all', name: 'all_listings')]
    public function all(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
                // Fetch all listings query
                $queryBuilder = $em->getRepository(Listing::class)->createQueryBuilder('l');

                // Paginate the results
                $pagination = $paginator->paginate(
                    $queryBuilder, /* query NOT result */
                    $request->query->getInt('page', 1), /* page number */
                    6 /* limit per page */
                );
        
                return $this->render('listing/all.html.twig', [
                    'pagination' => $pagination,
                ]);
            }
        
    

    #[Route('/listings', name: 'app_listing')]
    public function index(): Response
    {
        // Get the currently logged-in user
        $user = $this->getUser();
    
        // If the user is not logged in, handle this case as needed
        if (!$user instanceof User) {
            // Handle case when user is not logged in
            return $this->redirectToRoute('app_lucky_number');
        }
        // Fetch listings associated with the logged-in user
        $listings = $user->getListings();
      
        return $this->render('listing/index.html.twig', [
            'listings' => $listings,
        ]);
    }

    
    #[Route('/listing/new', name: 'listing_new', methods: ['GET', 'POST'])]
    public function new(Request $request, #[Autowire('%photos_directory%')] string $photoDir): Response
    {
        $listing = new Listing();
        // $user = new User;
        $user = $this->getUser(); // Get the currently authenticated user

        if (!$user instanceof User) {
            // Handle case when user is not logged in
            // For example, redirect to login page or display an error message
            return $this->redirectToRoute('app_lucky_number');
        }

        $form = $this->createForm(ListingType::class, $listing, [
            'user_id' => $listing->getUser(), // Pass the user ID to the form
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //  dd($photo = $form['photo']->getData());
            // Set the user on the listing
            $listing->setUser($user);
            // Handle the photo upload
            if ($photo = $form['photo']->getData()) {
               $filename = uniqid().'.'.$photo->guessExtension();
               $photo->move($photoDir, $filename);
               $listing->setPhoto($filename);
               } 
            // else {
            //     $listing->setPhoto('default_photo.jpeg');
            // }

            $this->em->persist($listing);
            $this->em->flush();

            return $this->redirectToRoute('app_listing');
        }

        return $this->render('listing/new.html.twig', [
            'listing' => $listing,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/listing/edit/{id}', name: 'edit_listing')]
    public function editListing(int $id, EntityManagerInterface $em): Response
    {
        $listing = $em->getRepository(Listing::class)->find($id);
        if (!$listing) {
            throw $this->createNotFoundException('The listing does not exist');
        }

        $form = $this->createForm(ListingType::class, $listing);

        return $this->render('listing/edit.html.twig', [
            'form' => $form->createView(),
            'listing' => $listing,
        ]);
    }

    #[Route('/listing/update/{id}', name: 'update_listing', methods: ['POST'])]
    public function updateListing(int $id, Request $request, EntityManagerInterface $em, #[Autowire('%photos_directory%')] string $photoDir): Response
    {
        $listing = $em->getRepository(Listing::class)->find($id);
        if (!$listing) {
            throw $this->createNotFoundException('The listing does not exist');
        }

        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the photo upload
            if ($photo = $form['photo']->getData()) {
                $filename = uniqid().'.'.$photo->guessExtension();
                $photo->move($photoDir, $filename);
                $listing->setPhoto($filename);
            } elseif (!$listing->getPhoto()) {
                $listing->setPhoto('default_photo.jpeg'); // Ensure this default photo exists in the uploads/photos directory
            }

            $em->flush();

            return $this->redirectToRoute('app_listing');
        }

        return $this->render('listing/edit.html.twig', [
            'form' => $form->createView(),
            'listing' => $listing,
        ]);
    }

    #[Route('/listing/delete/{id}', name: 'delete_listing', methods: ['POST'])]
    public function destroy(int $id, EntityManagerInterface $em): Response
    {
        $listing = $em->getRepository(Listing::class)->find($id);
        if (!$listing) {
            throw $this->createNotFoundException('The listing does not exist');
        }

        $em->remove($listing);
        $em->flush();

        return $this->redirectToRoute('app_listing'); // Redirect to the listing overview or any relevant page
    }
}