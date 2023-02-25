<?php

namespace App\Controller\Site;

use App\Entity\Boite;
use App\Entity\Occasion;
use App\Form\Site\CatalogueFiltersType;
use App\Repository\BoiteRepository;
use App\Repository\InformationsLegalesRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use App\Form\Site\CatalogueSearchBoiteType;
use App\Form\Site\SearchOccasionType;
use App\Repository\ConfigurationRepository;
use App\Repository\PartenaireRepository;
use App\Service\Utilities;

class CataloguesController extends AbstractController
{
    public function __construct(
        private InformationsLegalesRepository $informationsLegalesRepository,
        private PaginatorInterface $paginator,
        private PanierRepository $panierRepository,
        private Security $security,
        private ConfigurationRepository $configurationRepository,
        private Utilities $utilities
    )
    {
    }

    /**
     * @Route("/catalogue-pieces-detachees/", name="catalogue_pieces_detachees")
     */
    public function cataloguePiecesDetachees(
        EntityManagerInterface $entityManager,
        Request $request,
        BoiteRepository $boiteRepository,
        PartenaireRepository $partenaireRepository
        ): Response
    {

        $formBoiteSearch = $this->createForm(CatalogueSearchBoiteType::class);
        $formBoiteSearch->handleRequest($request);

        //on initialise les resultats a NULL
        $boites = NULL;

        //si on faite une recherche
        if(!is_null($formBoiteSearch->get('searchBoite')->getData()) && strlen($formBoiteSearch->get('searchBoite')->getData()) > 2){
            $recherche = str_replace(" ","%",$formBoiteSearch->get('searchBoite')->getData());
            $donnees = $boiteRepository->findBoiteInDatabase($recherche);

            $boites = $this->paginator->paginate(
                $donnees, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                50 /*limit per page*/
            );
        }

        $filter = $request->query->get('tri');
        $filters = array("nom", "editeur", "annee", "ajout");
        if(in_array($filter, $filters)) {
            if($filter == "ajout"){
                $tri = ['id' => 'DESC'];
            }else{
                $tri = [$filter => 'ASC'];
            }
        }else{
            $tri = ['id' => 'DESC'];
        }

        $formFilters = $this->createForm(CatalogueFiltersType::class, null, ['tri' => $filter]);
        $formFilters->handleRequest($request);

        $donnees = $entityManager
        ->getRepository(Boite::class)
        ->findBy(['isOnLine' => true], $tri);

        //si finalement pas eu de recherche ($boites == NULL)
        if(is_null($boites)){
            $boites = $this->paginator->paginate(
                $donnees, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                24 /*limit per page*/
            );
        }

        //dans tous les cas on cherches les partenaires avec un site web
        $partenaires = $partenaireRepository->findBy(['isDetachee' => true, 'isEcommerce' => true, 'isAfficherWhenRechercheCatalogueIsNull' => true, 'isOnLine' => true]);

        return $this->render('site/catalogues/catalogue_pieces_detachees.html.twig', [
            'boites' => $boites,
            'catalogueFiltersForm' => $formFilters->createView(),
            'tri' => $tri,
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'partenaires' => $partenaires,
            'boiteSearch' => $formBoiteSearch->createView(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/catalogue-pieces-detachees/boite-{id}/{slug}/{editeur}", name="catalogue_pieces_detachees_demande")
     */
    public function cataloguePiecesDetacheesDemande(
        EntityManagerInterface $entityManager,
        $id,
        $slug
        ): Response
    {

        $boite = $entityManager
        ->getRepository(Boite::class)
        ->findOneBy(['id' => $id, 'slug' => $slug, 'isOnLine' => true ]);

        if(empty($boite)){
            $this->addFlash('warning', 'MERCI DE NE PAS JOUER AVEC L\'URL...');
            return $this->redirectToRoute('catalogue_pieces_detachees');
        }else{

            return $this->render('site/catalogues/catalogue_pieces_detachees_demande.html.twig', [
                'boite' => $boite,
                'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
                'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
            ]);
        }
    }

    /**
     * @Route("/catalogue-jeux-occasion/", name="catalogue_jeux_occasion")
     */
    public function catalogueJeuxOccasion(
        EntityManagerInterface $entityManager,
        BoiteRepository $boiteRepository,
        PartenaireRepository $partenaireRepository,
        Request $request,
        ): Response
    {
        $boite = new Boite();
        $form = $this->createForm(SearchOccasionType::class, $boite);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $recherche = preg_replace('/\s+/', '%', $form->get('nom')->getData());
            $age = $form->get('age')->getData();
            $nbrJoueurs = $form->get('nbrJoueurs')->getData();

            $boites = $boiteRepository->findOccasionsMultiCritere($recherche,$age,$nbrJoueurs);

            $donnees = [];
            //on parcours le resultat precedent
            foreach($boites as $boite){
                $occasion = $entityManager->getRepository(Occasion::class)->findOneBy(['boite' => $boite, 'isOnLine' => true]);
                //si l'occasion est en ligne on met dans le tableau de donnees
                if($occasion){
                    $donnees[] = $occasion;
                }
            }

            if(count($donnees) > 0){
                $occasions = $this->paginator->paginate(
                    $donnees, /* query NOT result */
                    $request->query->getInt('page', 1), /*page number*/
                    100 /*limit per page => NOT LIMIT OR LIKE THAT*/
                );
            }else{
                $occasions = [];
            }

        }else{
            $donnees = $entityManager
            ->getRepository(Occasion::class)
            ->findBy(['isOnLine' => true], ['id' => "DESC"]);

            $occasions = $this->paginator->paginate(
                $donnees, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                12 /*limit per page*/
            );
        }

        //dans tous les cas on cherches les partenaires avec un site web
        $partenaires = $partenaireRepository->findBy(['isComplet' => true, 'isEcommerce' => true, 'isAfficherWhenRechercheCatalogueIsNull' => true, 'isOnLine' => true]);
        $infosAndConfig = $this->utilities->importConfigurationAndInformationsLegales();

        return $this->render('site/catalogues/catalogue_jeux_occasion.html.twig', [
            'occasions' => $occasions,
            'form' => $form->createView(),
            'infosAndConfig' => $infosAndConfig,
            'tva' => $this->utilities->calculTauxTva($infosAndConfig['legales']->getTauxTva()),
            'partenaires' => $partenaires,
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/catalogue-jeux-occasion/occasion-{id}/{slug}/{editeur}", name="catalogue_jeux_occasion_details")
     */
    public function catalogueJeuxOccasionDetails(EntityManagerInterface $entityManager, $id, $slug): Response
    {

        $infosAndConfig = $this->utilities->importConfigurationAndInformationsLegales();

        $occasion = $entityManager
        ->getRepository(Occasion::class)
        ->findOneBy(['id' => $id, 'isOnLine' => true ]);

        if(empty($occasion)){
            $this->addFlash('warning', 'OCCASION INCONNUE...');
            return $this->redirectToRoute('catalogue_jeux_occasion');
        }else{
            //on verifier qu'on a pas jouer avec l'url en retrouvant le slug de la boite
            $boite = $entityManager
            ->getRepository(Boite::class)
            ->findBy(['id' => $occasion->getBoite(), 'slug' => $slug ]);

            if(empty($boite)){
                $this->addFlash('warning', 'MERCI DE NE PAS JOUER AVEC L\'URL...');
                return $this->redirectToRoute('catalogue_jeux_occasion');
            }



            return $this->render('site/catalogues/catalogue_jeux_occasion_details.html.twig', [
                'occasion' => $occasion,
                'infosAndConfig' => $infosAndConfig,
                'tva' => $this->utilities->calculTauxTva($infosAndConfig['legales']->getTauxTva()),
                'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
            ]);
        }
    }
}
