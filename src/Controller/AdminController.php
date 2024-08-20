<?php

namespace App\Controller;

use App\Entity\Listing;
use App\Form\ListingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted as AttributeIsGranted;

#[Route('/admin')]
#[AttributeIsGranted('ROLE_EDITOR')]
class AdminController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em,)
    {
    }

    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/listings', name: 'admin_listings')]
    public function manageListings(): Response
    {
        $listings = $this->em->getRepository(Listing::class)->findAll();

        return $this->render('admin/listings.html.twig', [
            'listings' => $listings,
        ]);
    }

    #[Route('/listing/new', name: 'admin_listing_new')]
    public function newListing(Request $request, #[Autowire('%photos_directory%')] string $photoDir): Response
    {
        $listing = new Listing();
        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($photo = $form['photo']->getData()) {
                $filename = uniqid().'.'.$photo->guessExtension();
                $photo->move($photoDir, $filename);
                $listing->setPhoto($filename);
            }

            $this->em->persist($listing);
            $this->em->flush();

            return $this->redirectToRoute('admin_listings');
        }

        return $this->render('admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/listing/edit/{id}', name: 'admin_listing_edit')]
    public function editListing(int $id, Request $request, #[Autowire('%photos_directory%')] string $photoDir): Response
    {
        $listing = $this->em->getRepository(Listing::class)->find($id);
        if (!$listing) {
            throw $this->createNotFoundException('The listing does not exist');
        }

        $form = $this->createForm(ListingType::class, $listing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($photo = $form['photo']->getData()) {
                $filename = uniqid().'.'.$photo->guessExtension();
                $photo->move($photoDir, $filename);
                $listing->setPhoto($filename);
            }

            $this->em->flush();

            return $this->redirectToRoute('admin_listings');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/listing/delete/{id}', name: 'admin_listing_delete', methods: ['POST'])]
    public function deleteListing(int $id, Request $request): Response
    {
        $listing = $this->em->getRepository(Listing::class)->find($id);
        if (!$listing) {
            throw $this->createNotFoundException('The listing does not exist');
        }

        $this->em->remove($listing);
        $this->em->flush();

        return $this->redirectToRoute('admin_listings');
    }
}
