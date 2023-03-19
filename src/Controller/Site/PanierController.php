<?php

namespace App\Controller\Site;

use App\Entity\Panier;
use App\Entity\PanierImage;
use DateTimeImmutable;
use App\Repository\BoiteRepository;
use App\Repository\PanierRepository;
use App\Repository\AdresseRepository;
use App\Repository\ArticleRepository;
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
    private Utilities $utilities,
    private ArticleRepository $articleRepository,
    private BoiteRepository $boiteRepository
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
     * @Route("/panier/ajout-article", name="panier-ajout-article")
     */
    public function addpanierArticle(
        Request $request
        ): Response
    {

        $article = $this->articleRepository->findArticleDirect($request->request->get('article'), $request->request->get('qte') );
        $boite = $this->boiteRepository->findOneBy(['id' => $request->request->get('boite')]) ;
        $qte = $request->request->get('qte');

        //si l'occasion est disponible à la vente
        if(!is_null($article)){

            $panier = new Panier();
            //ici on ajoute les differentes infos
            $panier->setUser($this->security->getUser())
            ->setCreatedAt( new DateTimeImmutable('now'))
            ->setArticle($article)
            ->setArticleQuantity($qte)
            ->setEtat("panier");

            $this->em->persist($panier);
            $this->em->flush($panier);

            $newQte = $article->getQuantity() - $request->request->get('qte');
            $article->setQuantity($newQte);
            $this->em->persist($article);
            $this->em->flush();

            //on signal le changement
            $this->addFlash('success', 'Article mis dans votre panier!');
        }else{
            //on signal le changement
            $this->addFlash('danger', 'Article réservé à l\'instant par un autre utilisateur!');
        }

        return $this->redirectToRoute('catalogue_pieces_detachees_demande_direct', [
            'id' => $boite->getId(),
            'slug' => $boite->getSlug(),
            'editeur' => $boite->getEditeur()
        ]);
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
        $panier_articles = $this->panierRepository->findByUserAndNotNullColumn('article', $user);

        $livraison_adresses = $this->adresseRepository->findBy(['user' => $user, 'isFacturation' => null]);
        $facturation_adresses = $this->adresseRepository->findBy(['user' => $user, 'isFacturation' => true]);

        //on cherche l'user administrateur
        $userRetrait = $userRepository->findOneBy(['email' => 'ADMINISTRATION@ADMINISTRATION.FR']);
        $adresseRetrait = $this->adresseRepository->findOneBy(['user' => $userRetrait, 'isFacturation' => false]);

        if(count($panier_boites) < 1 && count($panier_occasions) < 1 && count($panier_articles) < 1){
            //on signal le changement
            $this->addFlash('warning', 'Votre panier semble vide!');
            return $this->redirectToRoute('accueil');
        }else{

            $infosAndConfig = $this->utilities->importConfigurationAndInformationsLegales();

            return $this->render('site/panier/panier.html.twig', [
                'panier_occasions' => $panier_occasions,
                'panier_boites' => $panier_boites,
                'panier_articles' => $panier_articles,
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
        if(is_null($lignePanier->getBoite()) && is_null($lignePanier->getArticle())){
            $occasion = $lignePanier->getOccasion();
            $occasion->setIsOnLine(true);
            $this->em->persist($occasion);
            $this->em->flush();
        }
        //si c'est un article
        if(!is_null($lignePanier->getArticle())){
            $article = $lignePanier->getArticle();
            $article->setQuantity($article->getQuantity() + $lignePanier->getArticleQuantity());
            $this->em->persist($article);
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
     * @Route("/panier/demande-de-devis-terminer/", name="panier-soumis")
     */
    public function panierDemandeDevisEnd(): Response
    {
        return $this->render('site/panier/demandeTerminee.html.twig', [
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier']),
            'emailRvj' => $this->getParameter('COMPTESMTP')
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
        $paniers = [];
        $paniers['panier_occasions'] = $this->panierRepository->findByUserAndNotNullColumn('occasion',$user);
        $paniers['panier_articles'] = $this->panierRepository->findByUserAndNotNullColumn('article', $user);

        if(count($paniers) < 1){
            //si y a rien
            $this->addFlash('warning', 'Demande inconnue!');
            return $this->redirectToRoute('accueil');
        }else{

        $setup = [];
        $totalOccasionsHT = 0;
        $totalArticlesHT = 0;
        $setup['adresseFacturation'] = $this->adresseRepository->findOneBy(['id' => $request->request->get('adresse_facturation')]);
        $setup['adresseLivraison'] = $this->adresseRepository->findOneBy(['id' => $request->request->get('adresse_livraison')]);
        $setup['token'] = $token;

        if(count($paniers['panier_occasions']) > 0){
            foreach($paniers['panier_occasions'] as $panier){
                $totalOccasionsHT += $panier->getOccasion()->getPriceHt();
            }
        }
        if(count($paniers['panier_articles']) > 0){
            foreach($paniers['panier_articles'] as $panier){
                $totalArticlesHT += $panier->getArticle()->getPriceHt() * $panier->getArticleQuantity();
            }
        }


        //on regarde si le user doit payer l'adhésion
        if($user->getMembership() < new DateTimeImmutable('now')){
            $configuration = $this->configurationRepository->findOneBy([]);
            $cost = $configuration->getCost();
            $setup['cost'] = $cost;
        }else{
            $setup['cost'] = 0;
        }

        $setup['totalHT'] = $totalOccasionsHT + $totalArticlesHT;

        //on sauvegarde dans la base
        $token = $this->documentService->fromPanierSaveDevisInDataBaseWithoutPiecesDetachees($user, $setup, $paniers, $demande);

        return $this->redirectToRoute('app_paiement', [
            'token' => $token
        ]);
        }
    }
}
