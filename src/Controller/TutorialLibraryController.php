<?php

namespace App\Controller;

use App\Entity\TutorialLibrary;
use App\Form\TutorialLibraryType;
use App\Repository\TutorialLibraryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tutorial/library')]
final class TutorialLibraryController extends AbstractController
{
    /*
     * Lists all TutorialLibrary entities
     */
    #[Route(name: 'app_tutorial_library_index', methods: ['GET'])]
    public function index(TutorialLibraryRepository $tutorialLibraryRepository): Response
    {
        return $this->render('tutorial_library/index.html.twig', [
            'tutorial_libraries' => $tutorialLibraryRepository->findAll(),
        ]);
    }
    
    /*
     * Create a TutorialLibrary entity
     */
    #[Route('/new', name: 'app_tutorial_library_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tutorialLibrary = new TutorialLibrary();
        $form = $this->createForm(TutorialLibraryType::class, $tutorialLibrary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tutorialLibrary);
            $entityManager->flush();

            return $this->redirectToRoute('app_tutorial_library_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tutorial_library/new.html.twig', [
            'tutorial_library' => $tutorialLibrary,
            'form' => $form,
        ]);
    }
    
    /*
     * Show a TutorialLibrary entity
     */
    #[Route('/{id}', name: 'app_tutorial_library_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(TutorialLibrary $tutorialLibrary): Response
    {
        return $this->render('tutorial_library/show.html.twig', [
            'tutorial_library' => $tutorialLibrary,
        ]);
    }
    
    /*
     * Edit a TutorialLibrary entity
     */
    #[Route('/{id}/edit', name: 'app_tutorial_library_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TutorialLibrary $tutorialLibrary, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TutorialLibraryType::class, $tutorialLibrary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tutorial_library_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tutorial_library/edit.html.twig', [
            'tutorial_library' => $tutorialLibrary,
            'form' => $form,
        ]);
    }
    
    /*
     * Delete a TutorialLibrary entity
     */
    #[Route('/{id}', name: 'app_tutorial_library_delete', methods: ['POST'])]
    public function delete(Request $request, TutorialLibrary $tutorialLibrary, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tutorialLibrary->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tutorialLibrary);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tutorial_library_index', [], Response::HTTP_SEE_OTHER);
    }
}
