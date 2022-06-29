<?php

namespace App\Controller\Admin;

use App\Repository\DocumentRepository;
use App\Repository\PanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_accueil")
     */
    public function index(PanierRepository $panierRepository, DocumentRepository $documentRepository): Response
    {
        $demandes = $panierRepository->findDemandesGroupeBy();

        $devisSupprimerParUtilisateurs = $documentRepository->findBy(['isDeleteByUser' => true]);

        return $this->render('admin/index.html.twig', [
            'demandes' => $demandes,
            'devisSupprimerParUtilisateurs' => $devisSupprimerParUtilisateurs
        ]);
    }
}
