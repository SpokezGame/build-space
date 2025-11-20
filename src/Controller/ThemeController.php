<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Tutorial;
use App\Entity\Theme;
use App\Entity\Member;
use App\Form\ThemeType;
use App\Form\TutorialType;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/theme')]
final class ThemeController extends AbstractController
{
    #[Route(name: 'app_theme_index', methods: ['GET'])]
    public function index(ThemeRepository $themeRepository): Response
    {
        if($this->isGranted('ROLE_ADMIN')) {
            $themes = $themeRepository->findAll();
        } else if($this->isGranted('ROLE_Member')){
            $publicThemes = $themeRepository->findBy(
                [
                    'published' => true,
                ]);
            $personalThemes = $themeRepository->findBy(
                [
                   'published' => false,
                   'member' => $this->getUser(),
                ]);
            $themes = array_merge($publicThemes, $personalThemes);
        } else {
            $themes = $themeRepository->findBy(
                [
                    'published' => true,   
                ]);
        }
        
        return $this->render('theme/index.html.twig', [
            'themes' => $themes,
        ]);
    }
    
    /**
     * Only for admin
     **/
    #[Route('/new/{id}', name: 'app_theme_new', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Member $member): Response
    {
        if(!($this->isGranted('ROLE_ADMIN') or ($this->getUser() and $this->getUser()->getId() == $member->getId()))){
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }
        
        $theme = new Theme();
        $theme->setMember($member);
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($theme);
            $entityManager->flush();
            
            $this->addFlash('message', 'The theme has been created.');

            return $this->redirectToRoute('app_member_show', ['id' => $member->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('theme/new.html.twig', [
            'theme' => $theme,
            'member' => $member,
            'form' => $form,
        ]);
    }
    
    #[Route('/newintheme/{id}', name: 'app_tutorial_newintheme', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function newInTheme(Request $request, EntityManagerInterface $entityManager, Theme $theme): Response
    {
        if(!($this->isGranted('ROLE_ADMIN') or ($this->getUser() and $this->getUser()->getId() == $theme->getMember()->getId()))){
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }
        
        $tutorial = new Tutorial();
        // already set a theme, so as to not need add that field in the form (in TutorialType)
        $tutorial->addTheme($theme);
        
        $form = $this->createForm(TutorialType::class, $tutorial);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tutorial);
            $entityManager->flush();
            
            $this->addFlash('message', 'The theme has been created.');
            
            return $this->redirectToRoute('app_theme_show',
                ['id' => $theme->getId()],
                Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('tutorial/newintheme.html.twig', [
            'theme' => $theme,
            'tutorial' => $tutorial,
            'form' => $form,
        ]);
    }

    /*
     * Show a Tutorial entity in a theme
     */
    #[Route('/{theme_id}/tutorial/{tutorial_id}', name: 'app_theme_tutorial_show', requirements: ['theme_id' => '\d+', 'tutorial_id' => '\d+'], methods: ['GET'])]
    public function tutorialShow(
            #[MapEntity(id: 'theme_id')]
        Theme $theme,
        #[MapEntity(id: 'tutorial_id')]
        Tutorial $tutorial
    ): Response
    {   
        if(! $theme->getTutorials()->contains($tutorial)) {
          throw $this->createNotFoundException("Couldn't find such a tutorial in this theme!");
        }
        
        return $this->render('theme/tutorialshow.html.twig', [
            'tutorial' => $tutorial,
            'theme' => $theme
        ]);
    }

    #[Route('/{id}', name: 'app_theme_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Theme $theme): Response
    {
        if(!($this->isGranted('ROLE_ADMIN') or ($this->getUser() and $this->getUser()->getId() == $theme->getMember()->getId()) or $theme->isPublished())){
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }
        // Image of a plus
        $add = new Image();
        $path = __DIR__ . "/../../public/images/fixtures/plus.png";
        
        $add->setImageName("plus.png");
        $add->setImageSize(filesize($path));
        $add->setUpdatedAt(new \DateTimeImmutable());
        
        return $this->render('theme/show.html.twig', [
            'theme' => $theme,
            'tutorials' => $theme->getTutorials(),
            'add' => $add
        ]);
    }

    #[Route('/{id}/edit', name: 'app_theme_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Theme $theme, EntityManagerInterface $entityManager): Response
    {
        if(!($this->isGranted('ROLE_ADMIN') or ($this->getUser() and $this->getUser()->getId() == $theme->getMember()->getId()))){
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }
        
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('message', 'The theme has been modified.');

            return $this->redirectToRoute('app_member_show', ['id' => $theme->getMember()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('theme/edit.html.twig', [
            'theme' => $theme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_theme_delete', methods: ['POST'])]
    public function delete(Request $request, Theme $theme, EntityManagerInterface $entityManager): Response
    {
        if(!($this->isGranted('ROLE_ADMIN') or ($this->getUser() and $this->getUser()->getId() == $theme->getMember()->getId()))){
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }
        
        if ($this->isCsrfTokenValid('delete'.$theme->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($theme);
            $entityManager->flush();
            
            $this->addFlash('message', 'The theme has been deleted.');
        }

        return $this->redirectToRoute('app_member_show', ['id' => $theme->getMember()->getId()], Response::HTTP_SEE_OTHER);
    }
}
