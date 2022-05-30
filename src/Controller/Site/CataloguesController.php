<?php

namespace App\Controller\Site;

use App\Entity\Boite;
use App\Entity\Occasion;
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
    public function cataloguePiecesDetachees(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {

        $donnees = $entityManager
        ->getRepository(Boite::class)
        ->findBy(['isOnLine' => true], ['id' => "DESC"]);

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
            'images' => $images
        ]);
    }

    /**
     * @Route("/catalogue-pieces-detachees/demande/{id}/{slug}/{editeur}", name="catalogue_pieces_detachees_demande")
     */
    public function cataloguePiecesDetacheesDemande(EntityManagerInterface $entityManager, $id): Response
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
                'images' => $images
            ]);
        }
    }

    /**
     * @Route("/catalogue-jeux-occasion/", name="catalogue_jeux_occasion")
     */
    public function catalogueJeuxOccasion(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {

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
            'images' => $images
        ]);
    }
}
