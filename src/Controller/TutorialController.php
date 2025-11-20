<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Image;
use App\Entity\Tutorial;
use App\Entity\Library;
use App\Form\TutorialType;
use App\Repository\TutorialRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * (ONLY FOR ADMIN)
     */
    #[Route('/new/{id}', name: 'app_tutorial_new', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Library $library): Response
    {
        if(!($this->isGranted('ROLE_ADMIN'))){
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }
        
        $tutorial = new Tutorial();
        $tutorial->setLibrary($library);
        
        $form = $this->createForm(TutorialType::class, $tutorial);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Upload of several images
            $uploadedFiles = $form->get('steps')->getData();
            
            foreach ($uploadedFiles as $file) {
                $image = new Image();
                $image->setImageFile($file);
                $tutorial->addStep($image);
                $entityManager->persist($image);
            }
            
            // Save in database
            $entityManager->persist($tutorial);
            $entityManager->flush();
            
            $this->addFlash('message', 'The tutorial has been created.');
            
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
        if(!($this->isGranted('ROLE_ADMIN') or ($this->getUser() and $this->getUser()->getId() == $tutorial->getMember()->getId()))){
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }
        
        $form = $this->createForm(TutorialType::class, $tutorial);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Upload of several images
            $uploadedFiles = $form->get('steps')->getData();
            
            foreach ($uploadedFiles as $file) {
                $image = new Image();
                $image->setImageFile($file);
                $tutorial->addStep($image);
                $entityManager->persist($image);
            }
            
            $entityManager->flush();
            
            $this->addFlash('message', 'The tutorial has been modified.');
            
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
        if(!($this->isGranted('ROLE_ADMIN') or ($this->getUser() and $this->getUser()->getId() == $tutorial->getMember()->getId()))){
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }
        
        if ($this->isCsrfTokenValid('delete'.$tutorial->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tutorial);
            $entityManager->flush();
            
            $this->addFlash('message', 'The tutorial has been deleted.');
        }
        
        return $this->redirectToRoute('app_library_show', ['id' => $tutorial->getLibrary()->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/tutorial/step/{id}/remove', requirements: ['id' => '\d+'], name: 'app_tutorial_remove_step')]
    public function removeStep(Image $image, EntityManagerInterface $entityManager)
    {
        if(!($this->isGranted('ROLE_ADMIN') or ($this->getUser() and $this->getUser()->getId() == $image->getTutorialSteps()->getMember()->getId()))){
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }
        
        $tutorial = $image->getTutorialSteps();
        $tutorial->removeStep($image);
        $entityManager->remove($image);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_tutorial_edit', ['id' => $tutorial->getId()]);
    }
    
    #[Route('/tutorial/{id}/step/remove/all', requirements: ['id' => '\d+'], name: 'app_tutorial_remove_all_steps')]
    public function removeAllSteps(Tutorial $tutorial, EntityManagerInterface $entityManager)
    {
        if(!($this->isGranted('ROLE_ADMIN') or ($this->getUser() and $this->getUser()->getId() == $tutorial->getMember()->getId()))){
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }
        
        foreach ($tutorial->getSteps() as $step) {
            $tutorial->removeStep($step);
            $entityManager->remove($step);
        }
        
        $entityManager->flush();
        
        return $this->redirectToRoute('app_tutorial_edit', ['id' => $tutorial->getId()]);
    }
}
