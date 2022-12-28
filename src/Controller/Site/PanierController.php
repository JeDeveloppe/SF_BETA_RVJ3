<?php

namespace App\Controller\Site;

use App\Entity\Panier;
use App\Entity\PanierImage;
use DateTimeImmutable;
use App\Repository\BoiteRepository;
use App\Repository\PanierRepository;
use App\Repository\AdresseRepository;
use App\Repository\ConfigurationRepository;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use App\Repository\UserRepository;
use App\Service\DocumentService;
use App\Service\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
public function __construct(
    private PanierRepository $panierRepository,
    private InformationsLegalesRepository $informationsLegalesRepository,
    private Security $security,
    private ConfigurationRepository $configurationRepository,
    private EntityManagerInterface $em,
    private OccasionRepository $occasionRepository,
    private AdresseRepository $adresseRepository,
    private DocumentService $documentService,
    private Utilities $utilities
)
{
}

    /**
     * @Route("/panier/ajout-pieces-detachees", name="panier-ajout-pieces-detachees")
     */
    public function addpanierDetachees(
        Request $request,
        BoiteRepository $boiteRepository): Response
    {

        $panier = new Panier();
        $idBoite = $request->request->get('boite');
        $files = $request->files->get('photo');

        //ici on ajoute les differentes infos
        $panier->setMessage($request->request->get('message'))
                ->setUser($this->security->getUser())
                ->setCreatedAt( new DateTimeImmutable('now'))
                ->setBoite($boiteRepository->find(['id' => $idBoite]))
                ->setEtat("panier");

        $this->em->persist($panier);
        $this->em->flush($panier);

        //pour chaque image on met dans la base
        if(count($files) > 0){
            foreach($files as $file){
                $imagePanier = new PanierImage();
                $imagePanier->setPanier($panier)->setImage(base64_encode(file_get_contents($file)));
                $this->em->persist($imagePanier);
            }
            $this->em->flush($imagePanier);
        }

        //on signal le changement
        $this->addFlash('success', 'Demande ajoutée au panier!');
        return $this->redirectToRoute('catalogue_pieces_detachees');
    }

    /**
     * @Route("/panier/ajout-jeu-occasion", name="panier-ajout-jeu-occasion")
     */
    public function addpanierOccasion(
        Request $request): Response
    {

        $idOccasion = $request->request->get('rvjc');
        $occasion = $this->occasionRepository->findOneBy(['id' => $idOccasion, 'isOnLine' => true]);

        //si l'occasion est disponible à la vente
        if(!is_null($occasion)){

            $panier = new Panier();
            //ici on ajoute les differentes infos
            $panier->setUser($this->security->getUser())
            ->setCreatedAt( new DateTimeImmutable('now'))
            ->setOccasion($this->occasionRepository->find(['id' => $idOccasion]))
            ->setEtat("panier");

            $this->em->persist($panier);
            $this->em->flush($panier);


            $occasion->setIsOnLine(false);
            $this->em->persist($occasion);
            $this->em->flush();

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
        UserRepository $userRepository): Response
    {

        $user = $this->security->getUser();
        $panier_occasions = $this->panierRepository->findByUserAndNotNullColumn('occasion',$user);
        $panier_boites = $this->panierRepository->findByUserAndNotNullColumn('boite', $user);

        $livraison_adresses = $this->adresseRepository->findBy(['user' => $user, 'isFacturation' => null]);
        $facturation_adresses = $this->adresseRepository->findBy(['user' => $user, 'isFacturation' => true]);

        //on cherche l'user administrateur
        $userRetrait = $userRepository->findOneBy(['email' => 'ADMINISTRATION@ADMINISTRATION.FR']);
        $adresseRetrait = $this->adresseRepository->findOneBy(['user' => $userRetrait, 'isFacturation' => false]);

        if(count($panier_boites) < 1 && count($panier_occasions) < 1){
            //on signal le changement
            $this->addFlash('warning', 'Votre panier semble vide!');
            return $this->redirectToRoute('accueil');
        }else{

            $infosAndConfig = $this->utilities->importConfigurationAndInformationsLegales();

            return $this->render('site/panier/panier.html.twig', [
                'panier_occasions' => $panier_occasions,
                'panier_boites' => $panier_boites,
                'infosAndConfig' => $infosAndConfig,
                'tva' => $this->utilities->calculTauxTva($infosAndConfig['legales']->getTauxTva()),
                'token' => $this->documentService->generateRandomString(),
                'livraison_adresses' => $livraison_adresses,
                'facturation_adresses' => $facturation_adresses,
                'adresseRetrait' => $adresseRetrait,
                'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
            ]);
        }
    }

    /**
     * @Route("/panier/delete/{id}", name="app_panier_delete")
     */
    public function panierDelete(
        $id): Response
    {

        $user = $this->security->getUser();

        $lignePanier = $this->panierRepository->findOneBy(['id' => $id, 'user' => $user]);

        if(is_null($lignePanier)){
            return $this->redirectToRoute('accueil');
        }

        //si c'est un occasion
        if(is_null($lignePanier->getBoite())){

            $occasion = $lignePanier->getOccasion();
            $occasion->setIsOnLine(true);
            $this->em->persist($occasion);
            $this->em->flush();
        }

        //dans tous les cas on supprime la ligne du panier
        $this->panierRepository->remove($lignePanier);

        //on signal le changement
        $this->addFlash('success', 'Ligne supprimée du panier!');

        //on retourne au panier
        return $this->redirectToRoute('app_panier');
        
    }


   /**
     * @Route("/panier/demande-de-devis/", name="panier-mise-en-devis")
     */
    public function panierMiseEnDevis(
        Request $request): Response
    {

        $paniers = $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier']);

        $facturation = $this->adresseRepository->find($request->request->get('adresse_facturation'));

        $livraison = $this->adresseRepository->find($request->request->get('adresse_livraison'));

        $lastEntryArray = end($paniers)->getId();

        foreach($paniers as $panier){
            $panier->setEtat('demandeDevis'.$lastEntryArray)
                    ->setLivraison($livraison->getFirstName().' '.$livraison->getLastName().'<br/>'.$livraison->getAdresse().'<br/>'.$livraison->getVille()->getVilleCodePostal().' '.$livraison->getVille()->getVilleNom().'<br/>'.$livraison->getVille()->getDepartement()->getPays()->getIsoCode())
                    ->setFacturation($facturation->getFirstName().' '.$facturation->getLastName().'<br/>'.$facturation->getAdresse().'<br/>'.$facturation->getVille()->getVilleCodePostal().' '.$facturation->getVille()->getVilleNom().'<br/>'.$facturation->getVille()->getDepartement()->getPays()->getIsoCode());
            $this->em->persist($panier);
        }
        
        $this->em->flush();

        return $this->redirectToRoute('panier-soumis');
    }

    /**
     * @Route("/panier/demande-de-devis/fin", name="panier-soumis")
     */
    public function panierDemandeDevisEnd(): Response
    {
        return $this->render('site/panier/demandeTerminee.html.twig', [
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }


    /**
     * @Route("/panier/paiement/{demande}/{token}", name="panier_paiement")
     */
    public function panierPaiement(
        $demande,
        $token,
        Request $request)
    {
        $user = $this->security->getUser();

        $paniers = $this->panierRepository->findBy(['user' => $user, 'etat' => $demande]);

        if(!$paniers){
            //si y a rien
            $this->addFlash('warning', 'Demande inconnue!');
            return $this->redirectToRoute('accueil');
        }else{

        $setup = [];
        $totalOccasionsHT = 0;
        $setup['adresseFacturation'] = $this->adresseRepository->findOneBy(['id' => $request->request->get('adresse_facturation')]);
        $setup['adresseLivraison'] = $this->adresseRepository->findOneBy(['id' => $request->request->get('adresse_livraison')]);
        $setup['token'] = $token;

        foreach($paniers as $panier){
            $totalOccasionsHT += $panier->getOccasion()->getPriceHt();
        }

        //on regarde si le user doit payer l'adhésion
        if($user->getMembership() < new DateTimeImmutable('now')){
            $configuration = $this->configurationRepository->findOneBy([]);
            $cost = $configuration->getCost();
            $setup['cost'] = $cost;
        }else{
            $setup['cost'] = 0;
        }

        $setup['totalOccasionsHT'] = $totalOccasionsHT;

        //on sauvegarde dans la base
        $token = $this->documentService->fromPanierSaveDevisInDataBaseOnlyOccasions($user, $setup, $paniers, $demande);

        return $this->redirectToRoute('app_paiement', [
            'token' => $token
        ]);
        }
    }
}
