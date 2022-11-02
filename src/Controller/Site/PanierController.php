<?php

namespace App\Controller\Site;

use App\Entity\Panier;
use DateTimeImmutable;
use App\Entity\Adresse;
use App\Form\AdresseLivraisonType;
use App\Entity\InformationsLegales;
use App\Repository\BoiteRepository;
use App\Form\AdresseFacturationType;
use App\Repository\PanierRepository;
use App\Repository\AdresseRepository;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use App\Service\DocumentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier/ajout-pieces-detachees", name="panier-ajout-pieces-detachees")
     */
    public function addpanierDetachees(Request $request, Security $security, BoiteRepository $boiteRepository, EntityManagerInterface $em): Response
    {

        $panier = new Panier();
        $idBoite = $request->request->get('idDuJeu');

        //ici on ajoute les differentes infos
        $panier->setMessage($request->request->get('message'))
                ->setUser($security->getUser())
                ->setCreatedAt( new DateTimeImmutable('now'))
                ->setBoite($boiteRepository->find(['id' => $idBoite]))
                ->setEtat("panier");

        $em->persist($panier);
        $em->flush($panier);

         //on signal le changement
         $this->addFlash('success', 'Demande ajoutée au panier!');
        return $this->redirectToRoute('catalogue_pieces_detachees');
    }

    /**
     * @Route("/panier/ajout-jeu-occasion", name="panier-ajout-jeu-occasion")
     */
    public function addpanierOccasion(Request $request, Security $security, OccasionRepository $occasionRepository, EntityManagerInterface $em): Response
    {

        $idOccasion = $request->request->get('rvjc');
        $occasion = $occasionRepository->findOneBy(['id' => $idOccasion, 'isOnLine' => true]);

        //si l'occasion est disponible à la vente
        if(!is_null($occasion)){

            $panier = new Panier();
            //ici on ajoute les differentes infos
            $panier->setUser($security->getUser())
            ->setCreatedAt( new DateTimeImmutable('now'))
            ->setOccasion($occasionRepository->find(['id' => $idOccasion]))
            ->setEtat("panier");

            $em->persist($panier);
            $em->flush($panier);


            $occasion->setIsOnLine(false);
            $em->merge($occasion);
            $em->flush();

            //on signal le changement
            $this->addFlash('success', 'Occasion mis dans votre panier!');
        }else{
            //on signal le changement
            $this->addFlash('danger', 'Occasion réservé à l\'instant par un autre utilisateur!');
        }

        return $this->redirectToRoute('catalogue_jeux_occasion');
    }

    /**
     * @Route("/panier", name="app_panier")
     */
    public function index(
        PanierRepository $panierRepository,
        Security $security, 
        AdresseRepository $adresseRepository,
        InformationsLegalesRepository $informationsLegalesRepository,
        DocumentService $documentService): Response
    {

        $user = $security->getUser();
        $panier_occasions = $panierRepository->findByUserAndNotNullColumn('occasion',$user);
        $panier_boites = $panierRepository->findByUserAndNotNullColumn('boite', $user);

        $livraison_adresses = $adresseRepository->findBy(['user' => $user, 'isFacturation' => null]);
        $facturation_adresses = $adresseRepository->findBy(['user' => $user, 'isFacturation' => true]);

        $adresseRetrait = $adresseRepository->findBy(['user' => 2, 'isFacturation' => null]);

        if(count($panier_boites) < 1 && count($panier_occasions) < 1){
            //on signal le changement
            $this->addFlash('warning', 'Votre panier semble vide!');
            return $this->redirectToRoute('accueil');
        }else{

            $informationsLegales = $informationsLegalesRepository->findAll();
            $tva = $informationsLegales[0]->getTauxTva();

            return $this->render('site/panier/panier.html.twig', [
                'panier_occasions' => $panier_occasions,
                'panier_boites' => $panier_boites,
                'tva' => $tva,
                'token' => $documentService->generateRandomString(),
                'livraison_adresses' => $livraison_adresses,
                'facturation_adresses' => $facturation_adresses,
                'informationsLegales' =>  $informationsLegales,
                'adresseRetrait' => $adresseRetrait,
            ]);
        }
    }

    /**
     * @Route("/panier/delete/{id}", name="app_panier_delete")
     */
    public function panierDelete($id, PanierRepository $panierRepository, Security $security, EntityManagerInterface $em): Response
    {

        $user = $security->getUser();

        $lignePanier = $panierRepository->findOneBy(['id' => $id, 'user' => $user]);

        if(is_null($lignePanier)){
            return $this->redirectToRoute('accueil');
        }

        //si c'est un occasion
        if(is_null($lignePanier->getBoite())){

            $occasion = $lignePanier->getOccasion();
            $occasion->setIsOnLine(true);
            $em->merge($occasion);
            $em->flush();
        }

        //dans tous les cas on supprime la ligne du panier
        $panierRepository->remove($lignePanier);

        //on signal le changement
        $this->addFlash('success', 'Ligne supprimée du panier!');

        //on retourne au panier
        return $this->redirectToRoute('app_panier');
        
    }


   /**
     * @Route("/panier/demande-de-devis/", name="panier-mise-en-devis")
     */
    public function panierMiseEnDevis(
        Request $request,
        PanierRepository $panierRepository,
        Security $security,
        AdresseRepository $adresseRepository,
        EntityManagerInterface $em): Response
    {

        $paniers = $panierRepository->findBy(['user' => $security->getUser(), 'etat' => 'panier']);

        $facturation = $adresseRepository->find($request->request->get('adresse_facturation'));

        $livraison = $adresseRepository->find($request->request->get('adresse_livraison'));

        $lastEntryArray = end($paniers)->getId();

        foreach($paniers as $panier){
            $panier->setEtat('demandeDevis'.$lastEntryArray)
                   ->setLivraison($livraison->getFirstName().' '.$livraison->getLastName().'<br/>'.$livraison->getAdresse().'<br/>'.$livraison->getVille()->getVilleCodePostal().' '.$livraison->getVille()->getVilleNom())
                   ->setFacturation($facturation->getFirstName().' '.$facturation->getLastName().'<br/>'.$facturation->getAdresse().'<br/>'.$facturation->getVille()->getVilleCodePostal().' '.$facturation->getVille()->getVilleNom());
            $em->merge($panier);
        }
        


        $em->flush();

        return $this->redirectToRoute('panier-soumis');
    }

    /**
     * @Route("/panier/demande-de-devis/fin", name="panier-soumis")
     */
    public function panierDemandeDevisEnd(InformationsLegalesRepository $informationsLegalesRepository): Response
    {
        return $this->render('site/panier/demandeTerminee.html.twig', [
            'informationsLegales' =>  $informationsLegalesRepository->findAll()
        ]);
    }
}
