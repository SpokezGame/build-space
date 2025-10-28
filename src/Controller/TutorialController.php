<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Tutorial;
use App\Entity\Library;
use App\Form\TutorialType;
use App\Repository\TutorialRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/tutorial')]
final class TutorialController extends AbstractController
{
    /**
     * List all Tutorials entities
     * 
     */
    #[Route(name: 'app_tutorial_index', methods: ['GET'])]
    public function index(TutorialRepository $tutorialRepository): Response
    {
        $tutorials = $tutorialRepository->findAll();
        
        return $this->render('tutorial/index.html.twig', [
            'tutorials' => $tutorials,
        ]);
    }
    
    /*
     * Create a Tutorial entity
     */
    #[Route('/new/{id}', name: 'app_tutorial_new', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Library $library): Response
    {
        $tutorial = new Tutorial();
        $tutorial->setLibrary($library);
        $form = $this->createForm(TutorialType::class, $tutorial);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tutorial);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_library_show', ['id' => $library->getId()], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('tutorial/new.html.twig', [
            'tutorial' => $tutorial,
            'library' => $library,
            'form' => $form,
        ]);
    }
    
    /*
     * Show a Tutorial entity
     */
    #[Route('/{id}', name: 'app_tutorial_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Tutorial $tutorial): Response
    {
        return $this->render('tutorial/show.html.twig', [
            'tutorial' => $tutorial,
        ]);
    }
    
    /*
     * Edit a Tutorial entity
     */
    #[Route('/{id}/edit', name: 'app_tutorial_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tutorial $tutorial, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TutorialType::class, $tutorial);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            return $this->redirectToRoute('app_library_show', ['id' => $tutorial->getLibrary()->getId()], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('tutorial/edit.html.twig', [
            'tutorial' => $tutorial,
            'form' => $form,
        ]);
    }
    
    /*
     * Delete a Tutorial entity
     */
    #[Route('/{id}', name: 'app_tutorial_delete', methods: ['POST'])]
    public function delete(Request $request, Tutorial $tutorial, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tutorial->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tutorial);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('app_library_show', ['id' => $tutorial->getLibrary()->getId()], Response::HTTP_SEE_OTHER);
    }
}
