<?php

namespace App\Controller\Site;

use App\Entity\Boite;
use App\Entity\Occasion;
use App\Form\CatalogueFiltersType;
use App\Repository\InformationsLegalesRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class CataloguesController extends AbstractController
{
    public function __construct(
        private InformationsLegalesRepository $informationsLegalesRepository,
        private PaginatorInterface $paginator,
        private PanierRepository $panierRepository,
        private Security $security
    )
    {
        
    }

    /**
     * @Route("/catalogue-pieces-detachees/", name="catalogue_pieces_detachees")
     */
    public function cataloguePiecesDetachees(
        EntityManagerInterface $entityManager,
        Request $request,
        ): Response
    {
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

        $form = $this->createForm(CatalogueFiltersType::class, null, ['tri' => $filter]);
        $form->handleRequest($request);

        $donnees = $entityManager
        ->getRepository(Boite::class)
        ->findBy(['isOnLine' => true], $tri);

        $boites = $this->paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            24 /*limit per page*/
        );

        return $this->render('site/catalogues/catalogue_pieces_detachees.html.twig', [
            'boites' => $boites,
            'catalogueFiltersForm' => $form->createView(),
            'tri' => $tri,
            'informationsLegales' =>  $this->informationsLegalesRepository->findAll(),
            'form' => $form->createView(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/catalogue-pieces-detachees/demande/{id}/{slug}/{editeur}", name="catalogue_pieces_detachees_demande")
     */
    public function cataloguePiecesDetacheesDemande(
        EntityManagerInterface $entityManager,
        $id,
        ): Response
    {

        $boites = $entityManager
        ->getRepository(Boite::class)
        ->findBy(['id' => $id, 'isOnLine' => true ]);

        if(empty($boites)){
            return $this->redirectToRoute('catalogue_pieces_detachees');
        }else{

            return $this->render('site/catalogues/catalogue_pieces_detachees_demande.html.twig', [
                'boites' => $boites,
                'informationsLegales' =>  $this->informationsLegalesRepository->findAll(),
                'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
            ]);
        }
    }

    /**
     * @Route("/catalogue-jeux-occasion/", name="catalogue_jeux_occasion")
     */
    public function catalogueJeuxOccasion(
        EntityManagerInterface $entityManager,
        Request $request,
        ): Response
    {
        $donnees = $entityManager
        ->getRepository(Occasion::class)
        ->findBy(['isOnLine' => true], ['id' => "DESC"]);

        $occasions = $this->paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12 /*limit per page*/
        );

        return $this->render('site/catalogues/catalogue_jeux_occasion.html.twig', [
            'occasions' => $occasions,
            'informationsLegales' =>  $this->informationsLegalesRepository->findAll(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/catalogue-jeux-occasion/{id}/{slug}/{editeur}", name="catalogue_jeux_occasion_details")
     */
    public function catalogueJeuxOccasionDetails(InformationsLegalesRepository $informationsLegalesRepository, EntityManagerInterface $entityManager, $id): Response
    {

        $informationsLegales = $this->informationsLegalesRepository->findAll();
        $tva = $informationsLegales[0]->getTauxTva();

        $occasion = $entityManager
        ->getRepository(Occasion::class)
        ->findBy(['id' => $id, 'isOnLine' => true ]);

        if(empty($occasion)){
            return $this->redirectToRoute('catalogue_jeux_occasion');
        }else{

            return $this->render('site/catalogues/catalogue_jeux_occasion_details.html.twig', [
                'occasions' => $occasion,
                'tva' => $tva,
                'informationsLegales' =>  $this->informationsLegalesRepository->findAll(),
                'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
            ]);
        }
    }
}
