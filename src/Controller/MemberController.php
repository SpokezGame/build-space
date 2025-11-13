<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Image;
use App\Entity\Member;
use App\Repository\MemberRepository;

#[Route('/member')]
final class MemberController extends AbstractController
{
    /*
     * Lists all Member entities
     */
    #[Route(name: 'app_member_index', methods: ['GET'])]
    public function index(MemberRepository $memberRepository): Response
    {
        return $this->render('member/index.html.twig', [
            'members' => $memberRepository->findAll(),
        ]);
    }
    
    /*
     * Show a Member entity
     */
    #[Route('/{id}', name: 'app_member_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Member $member): Response
    {
        // Image of a plus
        $add = new Image();
        $path = __DIR__ . "/../../public/images/fixtures/plus.png";
        
        $add->setImageName("plus.png");
        $add->setImageSize(filesize($path));
        $add->setUpdatedAt(new \DateTimeImmutable());
        
        return $this->render('member/show.html.twig', [
            'member' => $member,
            'add' => $add
        ]);
    }
}
