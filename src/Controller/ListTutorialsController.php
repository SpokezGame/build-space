<?php

namespace App\Controller;

use App\Entity\ListTutorials;
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
        $htmlpage = '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Liste des tutoriels</title>
    </head>
    <body>
        <h1>Liste des tutoriels :</h1>
        <ul>';
        
        $listsTutorials = $listTutorialsRepository->findAll();
        foreach($listsTutorials as $listTutorials) {
            $htmlpage .= '<li><a href=' . $this->generateUrl('list_tutorials_show', ['id' => $listTutorials->getId()]) . '>'.  $listTutorials->getAuthor() .'</a></li>';
        }
        
        $htmlpage .= '</ul>';
        
        $htmlpage .= '</body></html>';
        
        return new Response(
            $htmlpage,
            Response::HTTP_OK,
            array('content-type' => 'text/html')
            );
    }
    
    /**
     * Show a ListTutorials
     *
     * @param Integer $id (note that the id must be an integer)
     */
    #[Route('/list/tutorials/{id}', name: 'list_tutorials_show', requirements: ['id' => '\d+'])]
    public function show(ManagerRegistry $doctrine, $id) : Response
    {
        $listTutorialsRepo = $doctrine->getRepository(ListTutorials::class);
        $listTutorials = $listTutorialsRepo->find($id);
        
        if (!$listTutorials) {
            throw $this->createNotFoundException('The list of tutorials does not exist');
        }
        
        $res = '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Liste des tutoriels de ' . $listTutorials->getAuthor() . '</title>
    </head>
    <body>
        <h1>Liste des tutoriels de ' . $listTutorials->getAuthor() . ' :</h1>
        <ul>';
        
        foreach($listTutorials->getTutorials() as $tutorial){
            $res .= '<li>' . $tutorial->getName() . '</li>';
        }
        
        $res .= '</ul>';

        $res .= '<p/><a href="' . $this->generateUrl('list_tutorials_all') . '">Back</a>
</body></html>';
        
        return new Response($res);
    }
    

}
