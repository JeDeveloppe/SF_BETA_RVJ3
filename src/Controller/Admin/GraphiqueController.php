<?php

namespace App\Controller\Admin;

use App\Service\GraphiqueService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GraphiqueController extends AbstractController
{
    #[Route('/admin/graphique', name: 'admin_graphique')]
    public function index()
    {
        $year = date("Y");

        return $this->render('admin/graphique/index.html.twig', [
            'annee' => $year
        ]);

    }

    #[Route('/admin/graphique/annuel/{annee}', name: 'admin_graphique_ca-annuel')]
    public function graphiqueAnnuel(GraphiqueService $graphiqueService, $annee)
    {        
        if(is_null($annee) OR strlen($annee) < 4){

        }else{
            $graphiqueService->graphCA_Annuel($annee);
        }
    }

    #[Route('/admin/graphique/annuel-comparaison/', name: 'admin_graphique_ca-annuel-comparaison', methods: ['POST'])]
    public function graphiqueAnnuelComparaison(GraphiqueService $graphiqueService, Request $request)
    {
        $annee1 = $request->request->get('annee1');
        $annee2 = $request->request->get('annee2');
        
        if(is_null($annee1) OR is_null($annee2) OR strlen($annee1) < 4 OR strlen($annee2) < 4){

        }else{
            $graphiqueService->graphCA_Annuel_Comparaison($annee1,$annee2);
        }
    }
}
