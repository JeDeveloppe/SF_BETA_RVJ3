<?php

namespace App\Controller\Site;


use App\Repository\DocumentRepository;
use App\Repository\DocumentLignesRepository;
use App\Repository\InformationsLegalesRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteDocumentsController extends AbstractController
{
    /**
     * @Route("/devis/suppression-par-utilisateur/{token}", name="suppression_devis_par_utilisateur")
     */
    public function deleteByUser(
        $token,
        DocumentRepository $documentRepository,
        EntityManagerInterface $em,
        InformationsLegalesRepository $informationsLegalesRepository
        ): Response
    {
       //on cherche le devis par le token et s'il n'est pas deja annuler par l'utilisateur
        $devis = $documentRepository->findOneBy(['token' => $token, 'isDeleteByUser' => null]);

        if($devis == null){

            $tableau = [
                'h1' => 'Devis non trouvé !',
                'p1' => 'La consultation de ce devis est impossible!',
                'p2' => 'Devis inconnu ou supprimer !'
            ];

        }else{
            //on met a jour la base
            $devis->setIsDeleteByUser(true);
            $em->merge($devis);
            $em->flush();

            $tableau = [
                'h1' => 'Opération terminée !',
                'p1' => 'La consultation de ce devis est devenue impossible!',
                'p2' => 'Devis supprimé par vous même !'
            ]; 
        }
        
        return $this->render('site/devis/devis_end.html.twig', [
            'tableau' => $tableau,
            'informationsLegales' =>  $informationsLegalesRepository->findAll()
        ]);

    }

    /**
     * @Route("/devis/{token}", name="lecture_devis_avant_paiement")
     */
    public function lectureDevis(
        $token,
        DocumentRepository $documentRepository,
        DocumentLignesRepository $documentLignesRepository,
        InformationsLegalesRepository $informationsLegalesRepository
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
                'informationsLegales' =>  $informationsLegalesRepository->findAll()
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

            return $this->render('site/devis/lecture_devis.html.twig', [
                'devis' => $devis,
                'occasions' => $occasions,
                'boites' => $boites,
                'totalOccasions' => $totalOccasions,
                'totalDetachees' => $totalDetachees / 100,
                'informationsLegales' =>  $informationsLegalesRepository->findAll()
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
}
