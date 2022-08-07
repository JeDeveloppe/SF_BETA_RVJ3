<?php

namespace App\Controller\Site;

use App\Entity\Boite;
use App\Entity\Occasion;
use App\Form\CatalogueFiltersType;
use App\Repository\InformationsLegalesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CataloguesController extends AbstractController
{
    /**
     * @Route("/catalogue-pieces-detachees/", name="catalogue_pieces_detachees")
     */
    public function cataloguePiecesDetachees(
        EntityManagerInterface $entityManager,
        Request $request,
        PaginatorInterface $paginator,
        InformationsLegalesRepository $informationsLegalesRepository
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

        $boites = $paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            24 /*limit per page*/
        );
        //on va stocker les images
        $images = [];
        foreach ($boites as $key => $boite) {
            $images[$key] = stream_get_contents($boite->getImageBlob());
        }

        return $this->render('site/catalogues/catalogue_pieces_detachees.html.twig', [
            'boites' => $boites,
            'images' => $images,
            'catalogueFiltersForm' => $form->createView(),
            'tri' => $tri,
            'informationsLegales' =>  $informationsLegalesRepository->findAll(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/catalogue-pieces-detachees/demande/{id}/{slug}/{editeur}", name="catalogue_pieces_detachees_demande")
     */
    public function cataloguePiecesDetacheesDemande(
        EntityManagerInterface $entityManager,
        $id,
        InformationsLegalesRepository $informationsLegalesRepository
        ): Response
    {

        $boites = $entityManager
        ->getRepository(Boite::class)
        ->findBy(['id' => $id, 'isOnLine' => true ]);

        if(empty($boites)){
            return $this->redirectToRoute('catalogue_pieces_detachees');
        }else{


            $images = [];
            foreach ($boites as $key => $boite) {
                $images[$key] = stream_get_contents($boite->getImageBlob());
            }
    

            return $this->render('site/catalogues/catalogue_pieces_detachees_demande.html.twig', [
                'boites' => $boites,
                'images' => $images,
                'informationsLegales' =>  $informationsLegalesRepository->findAll()
            ]);
        }
    }

    /**
     * @Route("/catalogue-jeux-occasion/", name="catalogue_jeux_occasion")
     */
    public function catalogueJeuxOccasion(
        InformationsLegalesRepository $informationsLegalesRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        PaginatorInterface $paginator
        ): Response
    {

        $informationsLegales = $informationsLegalesRepository->findAll();
        $tva = $informationsLegales[0]->getTauxTva();

        $donnees = $entityManager
        ->getRepository(Occasion::class)
        ->findBy(['isOnLine' => true], ['id' => "DESC"]);

        $occasions = $paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12 /*limit per page*/
        );

   

        //on va stocker les images
        $images = [];
        foreach($occasions as $key => $occasion) {
            $images[$key] = stream_get_contents($occasion->getBoite()->getImageBlob());
        }


        
        return $this->render('site/catalogues/catalogue_jeux_occasion.html.twig', [
            'occasions' => $occasions,
            'images' => $images,
            'tva' => $tva,
            'informationsLegales' =>  $informationsLegalesRepository->findAll()
        ]);
    }

    /**
     * @Route("/catalogue-jeux-occasion/{id}/{slug}/{editeur}", name="catalogue_jeux_occasion_details")
     */
    public function catalogueJeuxOccasionDetails(InformationsLegalesRepository $informationsLegalesRepository, EntityManagerInterface $entityManager, $id): Response
    {

        $informationsLegales = $informationsLegalesRepository->findAll();
        $tva = $informationsLegales[0]->getTauxTva();

        $occasion = $entityManager
        ->getRepository(Occasion::class)
        ->findBy(['id' => $id, 'isOnLine' => true ]);

        if(empty($occasion)){
            return $this->redirectToRoute('catalogue_jeux_occasion');
        }else{


    
            $images = [];
            foreach ($occasion as $key => $occ) {
                $images[$key] = stream_get_contents($occ->getBoite()->getImageBlob());
            }
            

            return $this->render('site/catalogues/catalogue_jeux_occasion_details.html.twig', [
                'occasions' => $occasion,
                'images' => $images,
                'tva' => $tva,
                'informationsLegales' =>  $informationsLegalesRepository->findAll()
            ]);
        }
    }
}
