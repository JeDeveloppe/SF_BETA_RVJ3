<?php

namespace App\Controller\Admin;

use DateTimeImmutable;
use App\Entity\Paiement;
use App\Form\SearchDocumentType;
use App\Service\DocumentService;
use App\Form\DocumentPaiementType;
use App\Repository\PanierRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentLignesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDocumentsController extends AbstractController
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

            return $this->redirectToRoute('lecture_devis', [
                'numeroDevis' => $newNumero
            ]);
        }

    }

    /**
     * @Route("/admin/document/devis/lecture-devis/{numeroDevis}", name="lecture_devis")
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

            $moreRecentDevis = $documentRepository->findAMoreRecentDevis($numeroDevis);

            if(count($moreRecentDevis) == 1){
                $suppressionDevis = true;
            }else{
                $suppressionDevis = false;
            }

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
                'totalDetachees' => $totalDetachees / 100,
                'suppressionDevis' => $suppressionDevis
            ]);
        }
    }

    /**
     * @Route("/admin/document/devis/delete-devis/{numeroDevis}", name="delete_devis")
     */
    public function deleteDevis(
        $numeroDevis,
        DocumentRepository $documentRepository,
        DocumentLignesRepository $documentLignesRepository,
        EntityManagerInterface $em
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

 
            //on supprime les demandes de piece
            foreach($boites as $boite){
                $documentLignesRepository->remove($boite);
            }

            foreach($occasions as $Loccasion){
                //on recupere la boite et on met en ligne
                $occasion = $Loccasion->getOccasion();
                $occasion->setIsOnLine(true);
                $em->merge($occasion);
                //on supprime la ligne
                $documentLignesRepository->remove($Loccasion);
            }

            //finalement on supprime le document qui est en devis
            $documentRepository->remove($devis);

            $em->flush();

            //on signal le changement
            $this->addFlash('success', 'Devis supprimer!');
            //on retourne à l'accueil
            return $this->redirectToRoute('admin_accueil');
        }
    }

    /**
     * @Route("/admin/document/recherche", name="documents_recherche")
     */
    public function rechercheDocument(Request $request, DocumentRepository $documentRepository): Response
    {
        $form = $this->createForm(SearchDocumentType::class);
        $form->handleRequest($request);
      
        if ($form->isSubmitted() && $form->isValid()) {

            $column = $form->get('document')->getData();
            $number = $form->get('numero')->getData();

            $datas = $documentRepository->findByDocumentAndNumber($column, $number);
        

            return $this->renderForm('admin/documents/search.html.twig', [
                'form' => $form,
                'datas' => $datas
            ]);
        }


        return $this->renderForm('admin/documents/search.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @Route("/admin/document/visualisation/{token}", name="visualisation_document")
     */
    public function visualisationDocument(
        $token,
        DocumentRepository $documentRepository,
        DocumentLignesRepository $documentLignesRepository,
        Request $request,
        EntityManagerInterface $em,
        DocumentService $documentService
        ): Response
    {

        //on cherche le devis par le token
        $devis = $documentRepository->findOneBy(['token' => $token]);
        $numeroFacture = $devis->getNumeroFacture();

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

        $form = $this->createForm(DocumentPaiementType::class);
        $form->handleRequest($request);
      
        if($form->isSubmitted() && $form->isValid()) {
            
            $date =$form->get('date')->getData();

            $immutable = DateTimeImmutable::createFromMutable($date);

            $paiement = new Paiement();
            $paiement->setTimeTransaction($immutable)
                     ->setMoyenPaiement($form->get('moyenPaiement')->getData())
                     ->setTokenTransaction("SAISIE MANUELLE")
                     ->setDocument($devis);
                     
            $em->persist($paiement);
            $em->flush();

            //on signal le changement
            $this->addFlash('success', 'Paiement enregistré');

            //on genere le numero de facture
            $newNumero = $documentService->generateNewNumberOf('numeroFacture', 'getNumeroFacture');
            //on enregistre dans la BDD
            $devis->setNumeroFacture($newNumero);
            $em->merge($devis);
            $em->flush();

        }

        return $this->renderForm('admin/documents/visualisation_document.html.twig', [
            'devis' => $devis,
            'occasions' => $occasions,
            'boites' => $boites,
            'totalOccasions' => $totalOccasions,
            'totalDetachees' => $totalDetachees,
            'toDelete' => $numeroFacture ? $numeroFacture : null,
            'form' => $form
        ]);
    }

    /**
     * @Route("/admin/document/changement-disponibilite-en-ligne/{value?}/{token}", name="changement_disponibilite_en_ligne")
     */
    public function rendreDisponibleOuIndisponibleAlUtilisateur(
        $token,
        $value,
        DocumentRepository $documentRepository,
        EntityManagerInterface $em,
        Request $request
        ): Response
    {

        if($value == 0){
            $value = null;
        }

        //on cherche le devis par le token
        $devis = $documentRepository->findOneBy(['token' => $token]);

        $devis->setIsDeleteByUser($value);

        $em->merge($devis);
        $em->flush();
       
        //on signal le changement
        $this->addFlash('success', 'État du document mis à jour!');
        return $this->redirect($request->headers->get('referer'));
    }
}
