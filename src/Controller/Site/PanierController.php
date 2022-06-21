<?php

namespace App\Controller\Site;

use App\Entity\Panier;
use DateTimeImmutable;
use App\Entity\InformationsLegales;
use App\Repository\BoiteRepository;
use App\Repository\PanierRepository;
use App\Repository\AdresseRepository;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
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
        InformationsLegalesRepository $informationsLegalesRepository): Response
    {

        $user = $security->getUser();
        $panier_occasions = $panierRepository->findByUserAndNotNullColumn('occasion',$user);
        $panier_boites = $panierRepository->findByUserAndNotNullColumn('boite', $user);

        $livraison_adresses = $adresseRepository->findBy(['user' => $user, 'isFacturation' => null]);
        $facturation_adresses = $adresseRepository->findBy(['user' => $user, 'isFacturation' => true]);

        if(count($panier_boites) < 1 && count($panier_occasions) < 1){
            //on signal le changement
            $this->addFlash('warning', 'Votre panier semble vide!');
            return $this->redirectToRoute('accueil');
        }else{

            $informationsLegales = $informationsLegalesRepository->findAll();
            $tva = $informationsLegales[0]->getTauxTva();

            return $this->render('site/panier/index.html.twig', [
                'panier_occasions' => $panier_occasions,
                'panier_boites' => $panier_boites,
                'tva' => $tva,
                'livraison_adresses' => $livraison_adresses,
                'facturation_adresses' => $facturation_adresses
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
    public function panierMiseEnDevis(Request $request, PanierRepository $panierRepository, Security $security, EntityManagerInterface $em): Response
    {

        $paniers = $panierRepository->findBy(['user' => $security->getUser(), 'etat' => 'panier']);

        $facturation = $request->request->get('adresseFacturation');
        $livraison = $request->request->get('adresseLivraison');

        $lastEntryArray = end($paniers)->getId();

        foreach($paniers as $panier){
            $panier->setEtat('demandeDevis'.$lastEntryArray)
                   ->setLivraison(preg_replace('/\s\s+/', ' ', $livraison))
                   ->setFacturation(preg_replace('/\s\s+/', ' ', $facturation));
            $em->merge($panier);
        }
        
        $em->flush();

        return $this->redirectToRoute('panier-soumis');
    }

    /**
     * @Route("/panier/demande-de-devis/fin", name="panier-soumis")
     */
    public function panierDemandeDevisEnd(): Response
    {
        return $this->render('site/panier/demandeTerminee.html.twig');
    }
}
