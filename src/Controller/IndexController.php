<?php

namespace App\Controller;

use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
final class IndexController extends AbstractController
{
    /**
     * Default and first page
     */
    #[Route(name: 'index')]
    public function index(): Response
    {
        // Images of text : TUTORIALS, MEMBERS and THEMES
        $tutorials = new Image();
        $path = __DIR__ . "/../../public/images/screens/tutorials.png";
        
        $tutorials->setImageName("tutorials.png");
        $tutorials->setImageSize(filesize($path));
        $tutorials->setUpdatedAt(new \DateTimeImmutable());
        
        $members = new Image();
        $path = __DIR__ . "/../../public/images/screens/members.png";
        
        $members->setImageName("members.png");
        $members->setImageSize(filesize($path));
        $members->setUpdatedAt(new \DateTimeImmutable());
        
        $themes = new Image();
        $path = __DIR__ . "/../../public/images/screens/themes.png";
        
        $themes->setImageName("themes.png");
        $themes->setImageSize(filesize($path));
        $themes->setUpdatedAt(new \DateTimeImmutable());
        
        return $this->render('index/index.html.twig', [
            'tutorials' => $tutorials,
            'members' => $members,
            'themes' => $themes
        ]);
    }   
}
