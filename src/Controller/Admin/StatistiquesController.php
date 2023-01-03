<?php

namespace App\Controller\Admin;

use App\Repository\BoiteRepository;
use App\Repository\DocumentLignesRepository;
use App\Repository\OccasionRepository;
use Knp\Component\Pager\PaginatorInterface;
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
    public function statistiqueBoites(DocumentLignesRepository $documentLignesRepository, BoiteRepository $boiteRepository)
    {
        $recherches = $documentLignesRepository->findSoldedBoitesGroupeBy();

        $boites = [];
        $rang = [];

        foreach($recherches as $recherche){
            $rang['boite'] = $boiteRepository->findBy(['id' => $recherche['idBoite']]);
            $rang['total'] = $recherche['totalBoites'];
            $boites[] = $rang;
        }

        return $this->render('admin/statistiques/boites.html.twig', [
            'boites' => $boites,
        ]);
    }

    #[Route('/admin/statistiques/occasions/', name: 'admin_statistiques_occasions')]
    public function statistiqueOccasions(
        DocumentLignesRepository $documentLignesRepository,
        OccasionRepository $occasionRepository,
        PaginatorInterface $paginator)
    {
        $recherches = $documentLignesRepository->findSoldedOccasionGroupeBy();

        $donnees = [];
        $rang = [];

        foreach($recherches as $recherche){
            $occasion = $occasionRepository->findOneBy(['id' => $recherche['idOccasion']]);
            $rang['boite'] = $occasion->getBoite();
            $rang['total'] = $recherche['totalBoites'];
            $donnees[] = $rang;
        }

        $occasions = $paginator->paginate(
            $donnees, /* query NOT result */
            1, /*page number*/
            10 /*limit per page*/
        );

        return $this->render('admin/statistiques/occasions.html.twig', [
            'occasions' => $occasions,
        ]);
    }
}
