<?php

namespace App\Controller\Admin;

use App\Service\GraphiqueService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GraphiqueController extends AbstractController
{
    #[Route('/admin/graphique/', name: 'admin_graphique')]
    public function index()
    {

        $annee = date('Y');
        return $this->render('admin/graphique/index.html.twig', [
            'annee' => $annee
        ]);

    }

    #[Route('/admin/graphique/annuel-CA/{annee}', name: 'admin_graphique_ca-annuel', methods: ['GET'])]
    public function graphiqueAnnuel(GraphiqueService $graphiqueService, $annee)
    {        
        if(is_null($annee) || strlen($annee) < 4 || !preg_match('/^\d{4}$/',$annee)){

            //on signal le changement
            $this->addFlash('warning', 'Année inconnue');
            //on retourne au panier
            return $this->redirectToRoute('admin_accueil');

        }else{
    
            $graphiqueService->graphCA_Annuel($annee);
        }
    }

    #[Route('/admin/graphique/annuel-comparaison/{annee}', name: 'admin_graphique_ca-annuel-comparaison', methods: ['GET'])]
    public function graphiqueAnnuelComparaison(GraphiqueService $graphiqueService, $annee)
    {
        
        if(is_null($annee) OR strlen($annee) < 4 OR !preg_match('/^\d{4}$/',$annee)){

            //on signal le changement
            $this->addFlash('warning', 'Année inconnue');
            //on retourne au panier
            return $this->redirectToRoute('admin_accueil');

        }else{
            $graphiqueService->graphCA_Annuel_Comparaison($annee);
        }
    }

    #[Route('/admin/graphique/annuel-ventes/{annee}', name: 'admin_graphique_annuel_ventes', methods: ['GET'])]
    public function graphiqueVentesDonsBoites(GraphiqueService $graphiqueService, $annee)
    {
        
        if(is_null($annee) OR strlen($annee) < 4 OR !preg_match('/^\d{4}$/',$annee)){

            //on signal le changement
            $this->addFlash('warning', 'Année inconnue');
            //on retourne au panier
            return $this->redirectToRoute('admin_accueil');

        }else{
            $graphiqueService->graphVentes($annee);
        }
    }

    #[Route('/admin/graphique/adhesions/{annee}', name: 'admin_graphique_adhesions', methods: ['GET'])]
    public function graphiqueAdhesions(GraphiqueService $graphiqueService, $annee)
    {
        
        if(is_null($annee) OR strlen($annee) < 4 OR !preg_match('/^\d{4}$/',$annee)){

            //on signal le changement
            $this->addFlash('warning', 'Année inconnue');
            //on retourne au panier
            return $this->redirectToRoute('admin_accueil');

        }else{
            $graphiqueService->graphAdhesions($annee);
        }
    }
}
