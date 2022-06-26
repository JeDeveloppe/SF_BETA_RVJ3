<?php

namespace App\Controller\Admin;

use DateTimeImmutable;
use App\Entity\Document;
use App\Entity\MethodeEnvoi;
use App\Entity\DocumentLignes;
use App\Form\MethodeEnvoiType;
use App\Repository\DocumentRepository;
use App\Service\DocumentService;
use App\Repository\UserRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use App\Repository\MethodeEnvoiRepository;
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

            $panier_occasions = $panierRepository->findBy(['etat' => $slug, 'boite' => null]);
            $panier_boites = $panierRepository->findBy(['etat' => $slug, 'occasion' => null]);

            //ON FAIT LE TOTAL DES OCCASIONS
            $totalOccasions = 0;
            foreach($panier_occasions as $panier_occasion){
                $totalOccasions = $totalOccasions + $panier_occasion->getOccasion()->getPriceHt();
            }
        }

        return $this->render('admin/documents/creation_devis.html.twig', [
            'paniers' => $paniers,
            'panier_occasions' => $panier_occasions,
            'panier_boites' => $panier_boites,
            'tva' => $tva,
            'totalOccasions' => $totalOccasions
        ]);
    }

     /**
     * @Route("/admin/document/creation-devis/{demande}/{user}", name="document_creation_devis")
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
            $documentService->saveDevisInDataBase($user, $request, $paniers, $demande);

            //on supprime les entree du panier
            // $documentService->deletePanierFromUser($paniers);

            return $this->redirectToRoute('devis');
        }

    }
}
