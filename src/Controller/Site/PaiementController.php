<?php

namespace App\Controller\Site;

use App\Repository\InformationsLegalesRepository;
use App\Repository\PanierRepository;
use Exception;
use App\Service\PaiementService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class PaiementController extends AbstractController
{

    public function __construct(
        private PaiementService $paiementService,
        private InformationsLegalesRepository $informationsLegalesRepository,
        private PanierRepository $panierRepository,
        private Security $security
    ){  
    }

    /**
     * @Route("/paiement/{token}", name="app_paiement")
     */
    public function creationPaiement($token)
    {
        if($_ENV["PAIEMENT_MODULE"] == "STRIPE")
        {
            $session = $this->paiementService->creationPaiementWithStripe($token);
            return $this->redirect($session->url, 303);

        }else if($_ENV["PAIEMENT_MODULE"] == "PAYPLUG")
        {
            $payment_url = $this->paiementService->creationPaiementWithPayplug($token);
            return $this->redirect($payment_url, 303);

        }else{
            throw new Exception('PAIEMENT_MODULE IN .ENV FILE NOT INFORM');
        }
    }

    /**
     * @Route("/paiement/validation/{token}", name="paiement_success")
     */
    public function paiementSuccess($token)
    {
        if($_ENV["PAIEMENT_MODULE"] == "STRIPE")
        {
            $this->paiementService->paiementSuccessWithStripe($token);
        }else if($_ENV["PAIEMENT_MODULE"] == "PAYPLUG")
        {
            $response = $this->paiementService->paiementSuccessWithPayplug($token);

            //si on a bien vérifié le paiement
            if(array_key_exists('paiement', $response)){
                return $this->render('site/paiement/success.html.twig', [
                    'token' => $token,
                    'informationsLegales' =>  $this->informationsLegalesRepository->findAll(),
                    'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
                ]);
            }else{
                $this->addFlash('warning', $response['messageFlash']);
                return $this->redirectToRoute($response['route']);
            }


        }else{
            throw new Exception('PAIEMENT_MODULE IN .ENV FILE NOT INFORM');
        }
    }

    /**
     * @Route("/paiement/annulation-achat/{token}", name="paiement_canceled")
     */
    public function paiementCancel($token)
    {
        $document = $this->documentRepository->findOneBy(['token' => $token]);

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

    /**
     * @Route("/paiement/notificationUrl/{token}", name="paiement_notificationUrl")
     */
    public function notificationUrl($token)
    {
        if($_ENV["PAIEMENT_MODULE"] == "STRIPE")
        {
            $this->paiementService->notificationUrlWithStripe($token);
        }else if($_ENV["PAIEMENT_MODULE"] == "PAYPLUG")
        {
            $this->paiementService->notificationUrlWithPayplug($token);
        }else{
            throw new Exception('PAIEMENT_MODULE IN .ENV FILE NOT INFORM');
        }
    }
}