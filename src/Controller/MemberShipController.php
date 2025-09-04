<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MemberShipController extends AbstractController
{
    #[Route('/member/ship', name: 'app_member_ship')]
    public function index(): Response
    {
        return $this->render('member_ship/index.html.twig', [
            'controller_name' => 'MemberShipController',
        ]);
    }
}
