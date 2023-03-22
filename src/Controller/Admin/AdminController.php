<?php

namespace App\Controller\Admin;

use App\Repository\ConfigurationRepository;
use App\Repository\DocumentRepository;
use App\Repository\EtatDocumentRepository;
use App\Repository\PanierRepository;
use App\Service\Utilities;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_accueil")
     */
    public function adminIndex(
        PanierRepository $panierRepository,
        DocumentRepository $documentRepository,
        ConfigurationRepository $configurationRepository,
        Utilities $utilities
        ): Response
    {
        $demandes = $panierRepository->findDemandesGroupeBy();

        $devisSupprimerParUtilisateurs = $documentRepository->findDevisDeleteByUser();

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
            'infosAndConfig' => $utilities->importConfigurationAndInformationsLegales(),
            'delaiDevis' => $delaiDevis
        ]);
    }

     /**
     * @Route("/admin/commandes", name="admin_commandes")
     */
    public function adminCommandes(
        DocumentRepository $documentRepository,
        EtatDocumentRepository $etatDocumentRepository,
        ): Response
    {

        $etatApreparer = $etatDocumentRepository->findOneBy(['name' => 'A préparer']);
        $etatDeCote = $etatDocumentRepository->findOneBy(['name' => 'Mise de côté']);

        $commandesApreparer = $documentRepository->findBy(['etatDocument' => $etatApreparer]);
        $commandesDeCote = $documentRepository->findBy(['etatDocument' => $etatDeCote]);

        return $this->render('admin/commandes/commandes.html.twig', [
            'commandesApreparer' => $commandesApreparer,
            'commandesDeCote' => $commandesDeCote
        ]);
    }
}
