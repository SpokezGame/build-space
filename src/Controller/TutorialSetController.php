<?php

namespace App\Controller;

use App\Entity\Tutorial;
use App\Entity\TutorialSet;
use App\Form\TutorialSetType;
use App\Form\TutorialType;
use App\Repository\TutorialSetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tutorial/set')]
final class TutorialSetController extends AbstractController
{
    #[Route(name: 'app_tutorial_set_index', methods: ['GET'])]
    public function index(TutorialSetRepository $tutorialSetRepository): Response
    {
        return $this->render('tutorial_set/index.html.twig', [
            'tutorial_sets' => $tutorialSetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tutorial_set_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tutorialSet = new TutorialSet();
        $form = $this->createForm(TutorialSetType::class, $tutorialSet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tutorialSet);
            $entityManager->flush();

            return $this->redirectToRoute('app_tutorial_set_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tutorial_set/new.html.twig', [
            'tutorial_set' => $tutorialSet,
            'form' => $form,
        ]);
    }
    
    #[Route('/newintutorialset/{id}', name: 'app_tutorial_newintutorialset', methods: ['GET', 'POST'])]
    public function newInTutorialSet(Request $request, EntityManagerInterface $entityManager, TutorialSet $tutorialSet): Response
    {
        $todo = new Tutorial();
        // already set a tutorialSet, so as to not need add that field in the form (in TutorialType)
        $todo->addTutorialSet($tutorialSet);
        
        $form = $this->createForm(TutorialType::class, $todo);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($todo);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_tutorial_set_show',
                ['id' => $tutorialSet->getId()],
                Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('tutorial/newintutorial_set.html.twig', [
            'tutorialSet' => $tutorialSet,
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tutorial_set_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(TutorialSet $tutorialSet): Response
    {
        return $this->render('tutorial_set/show.html.twig', [
            'tutorial_set' => $tutorialSet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tutorial_set_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TutorialSet $tutorialSet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TutorialSetType::class, $tutorialSet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tutorial_set_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tutorial_set/edit.html.twig', [
            'tutorial_set' => $tutorialSet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tutorial_set_delete', methods: ['POST'])]
    public function delete(Request $request, TutorialSet $tutorialSet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tutorialSet->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tutorialSet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tutorial_set_index', [], Response::HTTP_SEE_OTHER);
    }
}
