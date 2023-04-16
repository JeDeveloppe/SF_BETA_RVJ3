<?php

namespace App\Controller\Site;

use App\Repository\ArticleRepository;
use App\Repository\PanierRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DocumentLignesRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use App\Service\Utilities;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteDocumentsController extends AbstractController
{
    public function __construct(
        private InformationsLegalesRepository $informationsLegalesRepository,
        private PanierRepository $panierRepository,
        private Security $security,
        private Utilities $utilities,
        private DocumentLignesRepository $documentLignesRepository,
        private EntityManagerInterface $em,
        private ArticleRepository $articleRepository
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

            $occasions = $documentLignesRepository->findBy(['document' => $devis, 'boite' => null, 'article' => null]);
            $boites = $documentLignesRepository->findBy(['document' => $devis, 'occasion' => null, 'article' => null]);
            $articles = $documentLignesRepository->findBy(['document' => $devis, 'occasion' => null, 'boite' => null]);

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

            //ON FAIT LE TOTAL DES ARTICLES
            $totalArticles = 0;
            foreach($articles as $article){
                $totalArticles = $totalArticles + $article->getPrixVente();
            }

            $tauxTva = $this->utilities->calculTauxTva($devis->getTauxTva());
            $module_paiement = $_ENV["PAIEMENT_MODULE"];

            return $this->render('site/devis/lecture_devis.html.twig', [
                'devis' => $devis,
                'occasions' => $occasions,
                'boites' => $boites,
                'articles' => $articles,
                'tauxTva' => $tauxTva,
                'module_paiement' => $module_paiement,
                'totalOccasions' => $totalOccasions * $tauxTva,
                'totalDetachees' => $totalDetachees * $tauxTva,
                'totalArticles' => $totalArticles * $tauxTva,
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

            $occasions = $this->documentLignesRepository->findBy(['document' => $devis, 'boite' => null, 'article' => null]);
            $boites = $this->documentLignesRepository->findBy(['document' => $devis, 'occasion' => null, 'article' => null]);
            $articles = $this->documentLignesRepository->findBy(['document' => $devis, 'occasion' => null, 'boite' => null]);

            //on supprime les demandes de piece
            if(count($boites) > 0){
                foreach($boites as $boite){
                    $this->documentLignesRepository->remove($boite);
                }
            }

            if(count($occasions) > 0){
                foreach($occasions as $Loccasion){
                    //on recupere la boite et on met en ligne
                    $occasion = $Loccasion->getOccasion();
                    $occasion->setIsOnLine(true);
                    $this->em->persist($occasion);
                    //on supprime la ligne
                    $this->documentLignesRepository->remove($Loccasion);
                }
            }

            if(count($articles) > 0){
                foreach($articles as $article){
                    $articleInDataBase = $this->articleRepository->find($article->getArticle());
                    $articleInDataBase->setQuantity($articleInDataBase->getQuantity() + $article->getQuantity());
                    $this->em->flush($articleInDataBase);

                    //on supprime la ligne
                    $this->documentLignesRepository->remove($article);
                }
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
