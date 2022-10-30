<?php

namespace App\Controller\Site;

use Slim\App;
use Stripe\Stripe;
use Slim\Http\Request;
use App\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaiementController extends AbstractController
{
    #[Route('/paiement/{token}', name: 'app_paiement')]
    public function creationPaiement($token, DocumentRepository $documentRepository): Response
    {

        $devis = $documentRepository->findActiveDevis($token);

        if(!$devis){
            //pas de devis
            $this->addFlash('warning', 'Devis inconnu!');
            return $this->redirectToRoute('accueil');
        }


        return $this->render('paiement/index.html.twig', [
            'controller_name' => 'PaiementController',
        ]);
    }

    /**
     * @Route("/paiement/validation", name="paiement_success")
     */
    public function paiementSuccess()
    {
        return $this->render('paiement/success.html.twig', [
            'controller_name' => 'PaiementController',
        ]);
    }

     /**
     * @Route("/paiement/annulation", name="paiement_canceled")
     */
    public function paiementCancel()
    {
        return $this->render('paiement/cancel.html.twig', [
            'controller_name' => 'PaiementController',
        ]);
    }
}
