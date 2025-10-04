<?php

namespace App\Controller;

use App\Entity\ListTutorials;
use App\Entity\Tutorial;
use App\Repository\ListTutorialsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListTutorialsController extends AbstractController
{
    /**
     * Lists all ListTutorials entities.
     */
    #[Route('/list/tutorials', name: 'list_tutorials_all')]
    public function index(ListTutorialsRepository $listTutorialsRepository): Response
    {
        $listsTutorials = $listTutorialsRepository->findAll();
        
        return $this->render('list_tutorials/index.html.twig', ['lists_tutorials' => $listsTutorials]);
    }
    
    /**
     * Show a ListTutorials
     *
     * @param Integer $id (note that the id must be an integer)
     */
    #[Route('/list/tutorials/{id}', name: 'list_tutorials_show', requirements: ['id' => '\d+'])]
    public function show(ManagerRegistry $doctrine, $id) : Response
    {
        $entityManager= $doctrine->getManager();
               
        $listTutorials = $entityManager->getRepository(ListTutorials::class)->findOneBy(['id' => $id]);
        
        $tutorials = $entityManager->getRepository(Tutorial::class)->findBy(['listTutorials' => $listTutorials]);
        
        return $this->render('list_tutorials/show.html.twig', ['tutorials' => $tutorials]);
    }
    

}
