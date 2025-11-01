<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Library;
use App\Entity\Tutorial;
use App\Form\LibraryType;
use App\Repository\LibraryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/library')]
final class LibraryController extends AbstractController
{
    /*
     * Lists all Library entities
     */
    #[Route(name: 'app_library_index', methods: ['GET'])]
    public function index(LibraryRepository $libraryRepository): Response
    {
        return $this->render('library/index.html.twig', [
            'libraries' => $libraryRepository->findAll(),
        ]);
    }
    
    /*
     * Create a Library entity
     */
    #[Route('/new', name: 'app_library_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $library = new Library();
        $form = $this->createForm(LibraryType::class, $library);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($library);
            $entityManager->flush();

            return $this->redirectToRoute('app_library_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('library/new.html.twig', [
            'library' => $library,
            'form' => $form,
        ]);
    }
    
    /*
     * Show a Library entity
     */
    #[Route('/{id}', name: 'app_library_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Library $library): Response
    {
        // Image of a plus
        $add = new Image();
        $path = __DIR__ . "/../../public/images/fixtures/plus.png";
        
        $add->setImageName("plus.png");
        $add->setImageSize(filesize($path));
        $add->setUpdatedAt(new \DateTimeImmutable());
        
        return $this->render('library/show.html.twig', [
            'library' => $library,
            'tutorials' => $library->getTutorials(),
            'add' => $add
        ]);
    }
    
    /*
     * Show a Tutorial entity in a library
     */
    #[Route('/{library_id}/tutorial/{tutorial_id}', name: 'app_library_tutorial_show', requirements: ['library_id' => '\d+', 'tutorial_id' => '\d+'], methods: ['GET'])]
    public function tutorialShow(
        #[MapEntity(id: 'library_id')]
        Library $library,
        #[MapEntity(id: 'tutorial_id')]
        Tutorial $tutorial
        ): Response
        {
            if(! $library->getTutorials()->contains($tutorial)) {
                throw $this->createNotFoundException("Couldn't find such a tutorial in this library!");
            }
            
            // if(! $library->isPublished()) {
            //   throw $this->createAccessDeniedException("You cannot access the requested ressource!");
            //}
            
            return $this->render('library/tutorialshow.html.twig', [
                'tutorial' => $tutorial,
                'library' => $library
            ]);
    }
    
    /*
     * Edit a Library entity
     */
    #[Route('/{id}/edit', name: 'app_library_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Library $library, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LibraryType::class, $library);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_library_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('library/edit.html.twig', [
            'library' => $library,
            'form' => $form,
        ]);
    }
    
    /*
     * Delete a Library entity
     */
    #[Route('/{id}', name: 'app_library_delete', methods: ['POST'])]
    public function delete(Request $request, Library $library, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$library->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($library);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tutorial_library_index', [], Response::HTTP_SEE_OTHER);
    }
}
