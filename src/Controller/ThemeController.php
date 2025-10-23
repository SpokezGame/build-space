<?php

namespace App\Controller;

use App\Entity\Tutorial;
use App\Entity\Theme;
use App\Form\ThemeType;
use App\Form\TutorialType;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/theme')]
final class ThemeController extends AbstractController
{
    #[Route(name: 'app_theme_index', methods: ['GET'])]
    public function index(ThemeRepository $themeRepository): Response
    {
        return $this->render('theme/index.html.twig', [
            'themes' => $themeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_theme_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($theme);
            $entityManager->flush();

            return $this->redirectToRoute('app_theme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('theme/new.html.twig', [
            'theme' => $theme,
            'form' => $form,
        ]);
    }
    
    #[Route('/newintheme/{id}', name: 'app_tutorial_newintheme', methods: ['GET', 'POST'])]
    public function newInTheme(Request $request, EntityManagerInterface $entityManager, Theme $theme): Response
    {
        $todo = new Tutorial();
        // already set a theme, so as to not need add that field in the form (in TutorialType)
        $todo->addTheme($theme);
        
        $form = $this->createForm(TutorialType::class, $todo);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($todo);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_theme_show',
                ['id' => $theme->getId()],
                Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('tutorial/newintheme.html.twig', [
            'theme' => $theme,
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_theme_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Theme $theme): Response
    {
        return $this->render('theme/show.html.twig', [
            'theme' => $theme,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_theme_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Theme $theme, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_theme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('theme/edit.html.twig', [
            'theme' => $theme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_theme_delete', methods: ['POST'])]
    public function delete(Request $request, Theme $theme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$theme->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($theme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_theme_index', [], Response::HTTP_SEE_OTHER);
    }
}
