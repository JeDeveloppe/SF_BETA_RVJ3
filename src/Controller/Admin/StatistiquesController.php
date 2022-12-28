<?php

namespace App\Controller\Admin;

use App\Repository\DocumentLignesRepository;
use App\Repository\DocumentRepository;
use App\Service\GraphiqueService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatistiquesController extends AbstractController
{
    #[Route('/admin/statistiques/', name: 'admin_statistiques')]
    public function index()
    {

        $annee = date('Y');
        return $this->render('admin/statistiques/index.html.twig', [
            'annee' => $annee
        ]);

    }

    #[Route('/admin/statistiques/boites/', name: 'admin_statistiques_boites')]
    public function statistiques(DocumentLignesRepository $documentLignesRepository)
    {
        $boites = $documentLignesRepository->findBoitesGroupeBy();
dd($boites);
        return $this->render('admin/statistiques/boites.html.twig', [
            'boites' => $boites
        ]);
    }
}
