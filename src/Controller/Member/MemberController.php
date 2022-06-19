<?php

namespace App\Controller\Member;

use App\Repository\AdresseRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends AbstractController
{
    /**
     * @Route("/membre", name="app_member")
     */
    public function index(): Response
    {
        return $this->render('member/index.html.twig', [
            'controller_name' => 'MemberController',
        ]);
    }

    /**
     * @Route("/membre/historique", name="app_member_historique")
     */
    public function membreHistorique(): Response
    {
        return $this->render('member/historique.html.twig', [
            'controller_name' => 'MemberController',
        ]);
    }
}
