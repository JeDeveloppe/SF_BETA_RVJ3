<?php

namespace App\Controller\Site;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Repository\PanierRepository;
use App\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PaiementController extends AbstractController
{

    public function __construct(
        private DocumentRepository $documentRepository,
        private InformationsLegalesRepository $informationsLegalesRepository,
        private PanierRepository $panierRepository,
        private Security $security){  
    }

    #[Route('/paiement/{token}', name: 'app_paiement')]
    public function creationPaiement($token, Request $request): Response
    {

        $document = $this->documentRepository->findOneBy(['token' => $token, 'numeroFacture' => NULL, 'paiement' => NULL]);

        if(!$document){
            //pas de devis
            $this->addFlash('warning', 'Devis inconnu!');
            return $this->redirectToRoute('accueil');
        }

        $stripe = new Stripe();
        $stripe->setApiKey($_ENV["STRIPE_SECRET"]);


        $session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    
                    'product_data' => [
                            'name' => 'Devis '.$document->getNumeroDevis(),
                        ],
                    'unit_amount' => $document->getTotalTTC(),
                ],
              'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('paiement_success', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('paiement_canceled', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
          ]);

        return $this->redirect($session->url, 303);
    }


    /**
     * @Route("/paiement/validation/{token}", name="paiement_success")
     */
        public function paiementSuccess($token)
    {
        $document = $this->documentRepository->findOneBy(['token' => $token, 'numeroFacture' => NULL, 'paiement' => NULL]);

        if(!$document){
            //pas de devis
            $this->addFlash('warning', 'Document inconnu!');
            return $this->redirectToRoute('accueil');

        }else{

            return $this->render('site/paiement/success.html.twig', [
                'token' => $token,
                'informationsLegales' =>  $this->informationsLegalesRepository->findAll(),
                'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
            ]);
        }
    }

     /**
     * @Route("/paiement/annulation-achat/{token}", name="paiement_canceled")
     */
    public function paiementCancel($token)
    {
        $document = $this->documentRepository->findOneBy(['token' => $token, 'numeroFacture' => NULL, 'paiement' => NULL]);

        if(!$document){
            //pas de devis
            $this->addFlash('warning', 'Document inconnu!');
            return $this->redirectToRoute('accueil');

        }else{

            return $this->render('site/paiement/cancel.html.twig', [
                'token' => $token,
                'informationsLegales' =>  $this->informationsLegalesRepository->findAll(),
                'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
            ]);
        }
    }
}
