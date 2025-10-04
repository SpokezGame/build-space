<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Tutorial;
use App\Repository\TutorialRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;

final class TutorialController extends AbstractController
{
    /**
     * List all Tutorials entities
     */
    #[Route('/tutorial/all', name: 'tutorial_all')]
    public function index(TutorialRepository $tutorialRepository): Response
    {
        $tutorials = $tutorialRepository->findAll();
        
        return $this->render('tutorial/index.html.twig', [
            'tutorials' => $tutorials,
        ]);
    }
    
    /**
     * Show a Tutorial
     *
     * @param Integer $id (note that the id must be an integer)
     */
    #[Route('/tutorial/{id}', name: 'tutorial_show', requirements: ['id' => '\d+'])]
    public function show(ManagerRegistry $doctrine, $id) : Response
    {
        $entityManager= $doctrine->getManager();
        
        $tutorial = $entityManager->getRepository(Tutorial::class)->findOneBy(['id' => $id]);
        
        return $this->render('tutorial/show.html.twig', ['tutorial' => $tutorial]);
    }
}
