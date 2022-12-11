<?php

namespace App\Controller\Admin;

use DateTime;
use DateInterval;
use DateTimeImmutable;
use App\Entity\Paiement;
use App\Service\MailerService;
use App\Form\Admin\SearchDocumentType;
use App\Service\DocumentService;
use App\Form\Admin\DocumentPaiementType;
use App\Repository\PanierRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConfigurationRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\DocumentLignesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDocumentsController extends AbstractController
{

    public function __construct(
        private PaginatorInterface $paginator,
        private DocumentRepository $documentRepository,
        private DocumentLignesRepository $documentLignesRepository,
        private EntityManagerInterface $em
    )
    {
    }

    /**
     * @Route("/admin/document/lecture-demande/{slug}", name="document_demande")
     */
    public function lectureDemande(
        $slug,
        PanierRepository $panierRepository,
        InformationsLegalesRepository $informationsLegalesRepository,
        ConfigurationRepository $configurationRepository): Response
    {

        $paniers = $panierRepository->findBy(['etat' => $slug]);

        if(!$paniers){
            //on signal le changement
            $this->addFlash('warning', 'État inconnu!');
            return $this->redirectToRoute('admin_accueil');
        }else{
            $informationsLegales = $informationsLegalesRepository->findOneBy([]);
            $tva = $informationsLegales->getTauxTva();

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
                'configurationSite' => $configurationRepository->findAll(),
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
        ): Response
    {

        $devis = $this->documentRepository->findOneBy(['numeroDevis' => $numeroDevis]);

        if($devis == null){
            //on signal le changement
            $this->addFlash('warning', 'Devis inconnu!');
            return $this->redirectToRoute('admin_accueil');
        }else{

            $moreRecentDevis = $this->documentRepository->findAMoreRecentDevis($numeroDevis);

            if(count($moreRecentDevis) == 1){
                $suppressionDevis = true;
            }else{
                $suppressionDevis = false;
            }

            $occasions = $this->documentLignesRepository->findBy(['document' => $devis, 'boite' => null]);
            $boites = $this->documentLignesRepository->findBy(['document' => $devis, 'occasion' => null]);

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
                'cost' => $devis->getCost(),
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
        ): Response
    {

        $devis = $this->documentRepository->findOneBy(['numeroDevis' => $numeroDevis]);

        if($devis == null){
            //on signal le changement
            $this->addFlash('warning', 'Devis inconnu!');
            return $this->redirectToRoute('admin_accueil');
        }else{

            $occasions = $this->documentLignesRepository->findBy(['document' => $devis, 'boite' => null]);
            $boites = $this->documentLignesRepository->findBy(['document' => $devis, 'occasion' => null]);

 
            //on supprime les demandes de piece
            foreach($boites as $boite){
                $this->documentLignesRepository->remove($boite);
            }

            foreach($occasions as $Loccasion){
                //on recupere la boite et on met en ligne
                $occasion = $Loccasion->getOccasion();
                $occasion->setIsOnLine(true);
                $this->em->persist($occasion);
                //on supprime la ligne
                $this->documentLignesRepository->remove($Loccasion);
            }

            //finalement on supprime le document qui est en devis
            $this->documentRepository->remove($devis);

            $this->em->flush();

            //on signal le changement
            $this->addFlash('success', 'Devis supprimer!');
            //on retourne à l'accueil
            return $this->redirectToRoute('admin_accueil');
        }
    }

    /**
     * @Route("/admin/document/recherche", name="documents_recherche")
     */
    public function rechercheDocument(Request $request): Response
    {
        $form = $this->createForm(SearchDocumentType::class);
        $form->handleRequest($request);
      
        if ($form->isSubmitted() && $form->isValid()) {

            $column = $form->get('document')->getData();
            $number = $form->get('numero')->getData();

            if($column == 'numeroDevis'){
                $donnees = $this->documentRepository->findOnlyDevis($number);
            }else{
                $donnees = $this->documentRepository->findOnlyFactures($number);
            }

            $datas = $this->paginator->paginate(
                $donnees, /* query NOT result */
                1, /*page number*/
                50 /*limit per page*/
            );

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
        Request $request,
        DocumentService $documentService
        ): Response
    {

        //on cherche le devis par le token
        $devis = $this->documentRepository->findOneBy(['token' => $token]);
        $numeroFacture = $devis->getNumeroFacture();

        $occasions = $this->documentLignesRepository->findBy(['document' => $devis, 'boite' => null]);
        $boites = $this->documentLignesRepository->findBy(['document' => $devis, 'occasion' => null]);

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
                     ->setMoyenPaiement('Enregistré manuellement en: '.$form->get('moyenPaiement')->getData())
                     ->setTokenTransaction("SAISIE MANUELLE")
                     ->setCreatedAt(new DateTimeImmutable('now'));

            $this->em->persist($paiement);
            $this->em->flush();

            //on signal le changement
            $this->addFlash('success', 'Paiement enregistré');

            //on genere le numero de facture
            $newNumero = $documentService->generateNewNumberOf('numeroFacture', 'getNumeroFacture');
            //on enregistre dans la BDD
            $devis->setNumeroFacture($newNumero)
                  ->setPaiement($paiement);
            $this->em->persist($devis);
            $this->em->flush();

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
        Request $request
        ): Response
    {

        if($value == 0){
            $value = null;
        }

        //on cherche le devis par le token
        $devis = $this->documentRepository->findOneBy(['token' => $token]);

        $devis->setIsDeleteByUser($value);

        $this->em->persist($devis);
        $this->em->flush();
       
        //on signal le changement
        $this->addFlash('success', 'État du document mis à jour!');
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/admin/download/facture/{token}", name="admin_facture_download")
     */
    public function factureDownload($token, DocumentService $documentService)
    {
        $documentService->factureToPdf($token);

        return new Response();
    }

    /**
     * @Route("/admin/email/prevenir-devis-disponible/{days}/{token}/", name="admin_prevenir_devis_disponible")
     */
    public function adminPrevenirDevisDisponible(
        $token,
        $days,
        Request $request,
        MailerService $mailerService,
        ConfigurationRepository $configurationRepository
        )
    {

        $compteSmtp = $this->getParameter('COMPTESMTP');
        $configurations = $configurationRepository->findAll();

        $devis = $this->documentRepository->findOneBy(['token' => $token]);

        if($days > 0){
            $now = new DateTimeImmutable();
            $endDevis = $now->add(new DateInterval('P'.$days.'D'));

            $devis->setEndValidationDevis($endDevis)
            ->setIsDeleteByUser(false)
            ->setIsRelanceDevis(true)
            ->setEnvoiEmailDevis($now);

            //on met a jour le document
            $this->em->persist($devis);
            $this->em->flush();
        }

        $host = $request->getSchemeAndHttpHost();
        $link = $request->getSchemeAndHttpHost().$this->generateUrl('lecture_devis_avant_paiement', ['token' => $token]);


            $mailerService->sendEmailWithTemplate(
                $devis->getUser()->getEmail(),
                $compteSmtp,
                "Devis ".$configurations[0]->getPrefixeDevis().$devis->getNumeroDevis()." disponible jusqu'au ".$devis->getEndValidationDevis()->format('d-m-Y'),
                'email/devis_disponible.html.twig',
                [
                    'link' => $link,
                    'document' => $devis,
                    'host' => $host
                ]
            );

        $this->addFlash('success', 'Mail envoyé pour prévenir de la mise à disposition du devis!');
        return $this->redirect($request->headers->get('referer'));
    }

     /**
     * @Route("admin/email/relance-devis/{token}/", name="admin_relance_devis")
     */
    public function relanceDevisDeXJours(
        $token,
        EntityManagerInterface $em,
        Request $request,
        ConfigurationRepository $configurationRepository,
        MailerService $mailerService
        ): Response
    {

            $compteSmtp = $this->getParameter('COMPTESMTP');
            $configurations = $configurationRepository->findAll();

            if(count($configurations) < 1){
                $delaiDevis = 2; //on relance de 2 jours minimum par defaut
            }else{
                $delaiDevis = $configurations[0]->getDevisDelayBeforeDelete();
            }

            //on cherche le devis par le token
            $devis = $this->documentRepository->findOneBy(['token' => $token]);

            //on recupere la date du jour et on ajoute X jours
            $now = new DateTimeImmutable();
            $endDevis = $now->add(new DateInterval('P'.$delaiDevis.'D'));
            
            $devis->setEndValidationDevis($endDevis)
                ->setIsRelanceDevis(1);

            $em->persist($devis);
            $em->flush();
        
            $host = $request->getSchemeAndHttpHost();
            $link = $request->getSchemeAndHttpHost().$this->generateUrl('lecture_devis_avant_paiement', ['token' => $token]);
    
        $mailerService->sendEmailWithTemplate(
            $devis->getUser()->getEmail(),
            $compteSmtp,
            "Rappel devis ".$configurations[0]->getPrefixeDevis().$devis->getNumeroDevis()." disponible jusqu'au ".$devis->getEndValidationDevis()->format('d-m-Y'),
            'email/relance_devis.html.twig',
            [
                'link' => $link,
                'document' => $devis,
                'host' => $host
            ]
        );

        //on signal le changement
        $this->addFlash('success', 'Devis relancer de '.$delaiDevis.' jours!');
        return $this->redirect($request->headers->get('referer'));
    }
}