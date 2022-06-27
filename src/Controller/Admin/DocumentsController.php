<?php

namespace App\Controller\Admin;

use App\Repository\DocumentLigneRepository;
use App\Service\DocumentService;
use App\Repository\PanierRepository;
use App\Repository\DocumentRepository;
use App\Repository\DocumentLignesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DocumentsController extends AbstractController
{
    /**
     * @Route("/admin/document/lecture-demande/{slug}", name="document_demande")
     */
    public function lectureDemande($slug, PanierRepository $panierRepository, InformationsLegalesRepository $informationsLegalesRepository): Response
    {

        $paniers = $panierRepository->findBy(['etat' => $slug]);

        if($paniers == null){
            //on signal le changement
            $this->addFlash('warning', 'État inconnu!');
            return $this->redirectToRoute('admin_accueil');
        }else{
            $informationsLegales = $informationsLegalesRepository->findAll();
            $tva = $informationsLegales[0]->getTauxTva();

            $occasions = $panierRepository->findBy(['etat' => $slug, 'boite' => null]);
            $boites = $panierRepository->findBy(['etat' => $slug, 'occasion' => null]);

            //ON FAIT LE TOTAL DES OCCASIONS
            $totalOccasions = 0;
            foreach($occasions as $occasion){
                $totalOccasions = $totalOccasions + $occasion->getOccasion()->getPriceHt();
            }


            return $this->render('admin/documents/demandes/lecture_demande.html.twig', [
                'paniers' => $paniers,
                'occasions' => $occasions,
                'boites' => $boites,
                'tva' => $tva,
                'totalOccasions' => $totalOccasions
            ]);
        }
    }

     /**
     * @Route("/admin/document/demande/{demande}/{user}", name="document_creation_devis")
     */
    public function creationDevis(
        $demande,
        $user,
        Request $request,
        PanierRepository $panierRepository,
        DocumentService $documentService
        ): Response
    {
        //on cherche si y a bien quelque chose dans la table panier en fonction du bouton choisi
        $paniers = $panierRepository->findBy(['user' => $user, 'etat' => $demande]);

        if($paniers == null){
            //si y a rien
            $this->addFlash('warning', 'Demande inconnue!');
            return $this->redirectToRoute('admin_accueil');
        }else{

            //on sauvegarde dans la base
            $newNumero = $documentService->saveDevisInDataBase($user, $request, $paniers, $demande);

            //on supprime les entree du panier
            $documentService->deletePanierFromUser($paniers);

            return $this->redirectToRoute('document_lecture_devis', [
                'devis' => $newNumero
            ]);
        }

    }

      /**
     * @Route("/admin/document/devis/lecture-devis/{numeroDevis}", name="document_lecture_devis")
     */
    public function lectureDevis(
        $numeroDevis,
        DocumentRepository $documentRepository,
        DocumentLignesRepository $documentLignesRepository
        ): Response
    {

        $devis = $documentRepository->findOneBy(['numeroDevis' => $numeroDevis]);

        if($devis == null){
            //on signal le changement
            $this->addFlash('warning', 'Devis inconnu!');
            return $this->redirectToRoute('admin_accueil');
        }else{

            $occasions = $documentLignesRepository->findBy(['document' => $devis, 'boite' => null]);
            $boites = $documentLignesRepository->findBy(['document' => $devis, 'occasion' => null]);

            //ON FAIT LE TOTAL DES OCCASIONS
            $totalOccasions = 0;
            foreach($occasions as $occasion){
                $totalOccasions = $totalOccasions + $occasion->getOccasion()->getPriceHt();
            }

            //ON FAIT LE TOTAL DES PIECES DETACHEES
            $totalDetachees = 0;
            foreach($boites as $boite){
                $totalDetachees = $totalDetachees + $boite->getPrixVente();
            }

            return $this->render('admin/documents/devis/lecture_devis.html.twig', [
                'devis' => $devis,
                'occasions' => $occasions,
                'boites' => $boites,
                'totalOccasions' => $totalOccasions,
                'totalDetachees' => $totalDetachees / 100
            ]);
        }
    }
}
