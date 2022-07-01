<?php

namespace App\Controller\Admin;

use App\Repository\ConfigurationRepository;
use App\Repository\DocumentRepository;
use App\Repository\PanierRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_accueil")
     */
    public function index(
        PanierRepository $panierRepository,
        DocumentRepository $documentRepository,
        ConfigurationRepository $configurationRepository
        ): Response
    {
        $demandes = $panierRepository->findDemandesGroupeBy();

        $devisSupprimerParUtilisateurs = $documentRepository->findBy(['isDeleteByUser' => true]);

        $devisArelancer = $documentRepository->findDevisEndDelay(new DateTimeImmutable());

        $configurations = $configurationRepository->findAll();

        if(count($configurations) < 1){
            $delaiDevis = "PAS CONFIGURER";
        }else{
            $delaiDevis = $configurations[0]->getDevisDelayBeforeDelete();
        }

        return $this->render('admin/index.html.twig', [
            'demandes' => $demandes,
            'relances' => $devisArelancer,
            'devisSupprimerParUtilisateurs' => $devisSupprimerParUtilisateurs,
            'delaiDevis' => $delaiDevis
        ]);
    }
}
