<?php

namespace App\Controller\Site;


use App\Repository\PanierRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentLignesRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use App\Service\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteDocumentsController extends AbstractController
{
    public function __construct(
        private InformationsLegalesRepository $informationsLegalesRepository,
        private PanierRepository $panierRepository,
        private Security $security,
        private Utilities $utilities
    )
    {
        
    }
    /**
     * @Route("/devis/suppression-par-utilisateur/{token}", name="suppression_devis_par_utilisateur")
     */
    public function deleteByUser(
        $token,
        DocumentRepository $documentRepository,
        EntityManagerInterface $em,
        ): Response
    {
       //on cherche le devis par le token et s'il n'est pas deja annuler par l'utilisateur
        $devis = $documentRepository->findOneBy(['token' => $token, 'isDeleteByUser' => false]);

        if($devis == null){

            $tableau = [
                'h1' => 'Devis non trouvé !',
                'p1' => 'La consultation de ce devis est impossible!',
                'p2' => 'Devis inconnu ou supprimer !'
            ];

        }else{
            //on met a jour la base
            $devis->setIsDeleteByUser(true);
            $em->persist($devis);
            $em->flush();

            $tableau = [
                'h1' => 'Opération terminée !',
                'p1' => 'La consultation de ce devis est devenue impossible!',
                'p2' => 'Devis supprimé par vous même !'
            ]; 
        }
        
        return $this->render('site/devis/devis_end.html.twig', [
            'tableau' => $tableau,
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);

    }

    /**
     * @Route("/devis/{token}", name="lecture_devis_avant_paiement")
     */
    public function lectureDevis(
        $token,
        DocumentRepository $documentRepository,
        DocumentLignesRepository $documentLignesRepository,
        ): Response
    {

        //on cherche le devis par le token et s'il n'est pas deja annuler par l'utilisateur
        // $devis = $documentRepository->findOneBy(['token' => $token, 'isDeleteByUser' => null, 'numeroFacture' => null]);

        $devis = $documentRepository->findActiveDevis($token);

        if($devis == null){

            $tableau = [
                'h1' => 'Devis non trouvé !',
                'p1' => 'La consultation de ce devis est impossible!',
                'p2' => 'Devis inconnu, supprimer ou déjà facturé !'
            ];

            return $this->render('site/devis/devis_end.html.twig', [
                'tableau' => $tableau,
                'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
                'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
            ]);

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

            $tauxTva = $this->utilities->calculTauxTva($devis->getTauxTva());
            $module_paiement = $_ENV["PAIEMENT_MODULE"];

            return $this->render('site/devis/lecture_devis.html.twig', [
                'devis' => $devis,
                'occasions' => $occasions,
                'boites' => $boites,
                'tauxTva' => $tauxTva,
                'module_paiement' => $module_paiement,
                'totalOccasions' => $totalOccasions * $tauxTva,
                'totalDetachees' => $totalDetachees * $tauxTva,
                'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
                'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
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
                $em->persist($occasion);
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
}
