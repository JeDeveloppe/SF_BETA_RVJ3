<?php

namespace App\Controller\Admin;

use App\Repository\PanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_accueil")
     */
    public function index(PanierRepository $panierRepository): Response
    {
        $demandes = $panierRepository->findDemandesGroupeBy();

        return $this->render('admin/index.html.twig', [
            'demandes' => $demandes,
        ]);
    }
}
