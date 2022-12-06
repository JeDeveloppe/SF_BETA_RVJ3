<?php

namespace App\Controller\Site;

use App\Entity\Paiement;
use Stripe\Stripe;
use Payplug\Payment;
use Payplug\Payplug;
use Stripe\Checkout\Session;
use App\Repository\PanierRepository;
use App\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use App\Repository\PaiementRepository;
use App\Service\DocumentService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PaiementController extends AbstractController
{

    public function __construct(
        private DocumentRepository $documentRepository,
        private InformationsLegalesRepository $informationsLegalesRepository,
        private PanierRepository $panierRepository,
        private PaiementRepository $paiementRepository,
        private EntityManagerInterface $em,
        private Security $security){  
    }

    // #[Route('/paiement/{token}', name: 'app_paiement')]
    // public function creationPaiement($token, Request $request): Response
    // {

    //     $document = $this->documentRepository->findOneBy(['token' => $token, 'numeroFacture' => NULL, 'paiement' => NULL]);

    //     if(!$document){
    //         //pas de devis
    //         $this->addFlash('warning', 'Devis inconnu!');
    //         return $this->redirectToRoute('accueil');
    //     }

    //     $stripe = new Stripe();
    //     $stripe->setApiKey($_ENV["STRIPE_SECRET"]);


    //     $session = Session::create([
    //         'line_items' => [[
    //             'price_data' => [
    //                 'currency' => 'eur',
                    
    //                 'product_data' => [
    //                         'name' => 'Devis '.$document->getNumeroDevis(),
    //                     ],
    //                 'unit_amount' => $document->getTotalTTC(),
    //             ],
    //           'quantity' => 1,
    //         ]],
    //         'mode' => 'payment',
    //         'success_url' => $this->generateUrl('paiement_success', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
    //         'cancel_url' => $this->generateUrl('paiement_canceled', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
    //       ]);

    //     return $this->redirect($session->url, 303);
    // }

    #[Route('/paiement/{token}', name: 'app_paiement')]
    public function creationPaiement($token,
    EntityManagerInterface $em,
    ): Response
    {

        $document = $this->documentRepository->findOneBy(['token' => $token, 'numeroFacture' => NULL, 'paiement' => NULL, 'isDeleteByUser' => null]);

        if(!$document){
            //pas de devis
            $this->addFlash('warning', 'Devis inconnu!');
            return $this->redirectToRoute('accueil');
        }


        \Payplug\Payplug::setSecretKey($_ENV["PAYPLUG_SECRET"]);

        $customer_id = $document->getToken();
        
        $arrayAdresseF = $this->explodeAdresse($document->getAdresseFacturation(),$document);
        $arrayAdresseL = $this->explodeAdresse($document->getAdresseLivraison(),$document);

        $payment = \Payplug\Payment::create([
                'amount'            => $document->getTotalTTC(),
                'currency'          => 'EUR',
                'billing'          => [
                    'title'        => $arrayAdresseF['title'],
                    'first_name'   => $arrayAdresseF['first_name'],
                    'last_name'    => $arrayAdresseF['last_name'],
                    'email'        => $arrayAdresseF['email'],
                    'address1'     => $arrayAdresseF['adresse1'],
                    'postcode'     => $arrayAdresseF['postCode'],
                    'city'         => $arrayAdresseF['city'],
                    'country'      => $arrayAdresseF['country'],
                    'language'     => $arrayAdresseF['language']
                ],
                'shipping'          => [
                    'title'        => $arrayAdresseL['title'],
                    'first_name'   => $arrayAdresseL['first_name'],
                    'last_name'    => $arrayAdresseL['last_name'],
                    'email'        => $arrayAdresseL['email'],
                    'address1'     => $arrayAdresseL['adresse1'],
                    'postcode'     => $arrayAdresseL['postCode'],
                    'city'         => $arrayAdresseL['city'],
                    'country'      => $arrayAdresseL['country'],
                    'language'     => $arrayAdresseL['language'],
                    'delivery_type' => 'BILLING'
                ],
                'hosted_payment' => [
                    'return_url' => $this->generateUrl('paiement_success', ['token' => $customer_id], UrlGeneratorInterface::ABSOLUTE_URL),
                    'cancel_url' => $this->generateUrl('paiement_canceled', ['token' => $customer_id], UrlGeneratorInterface::ABSOLUTE_URL)
                ],
                'notification_url' => $this->generateUrl('paiement_notificationUrl', ['token' => $customer_id], UrlGeneratorInterface::ABSOLUTE_URL),
                'metadata'         => [
                    'customer_id'  => $customer_id
                ]
        ]);

        $payment_url = $payment->hosted_payment->payment_url;
        $payment_id = $payment->id;

        //on a l'id de paiement donc on met a jour la table paiement
        $paiement = new Paiement();
        $paiement->setDocument($document)
                ->setTokenTransaction($payment_id)
                ->setCreatedAt(new DateTimeImmutable('now'));
        
        $em->persist($paiement);

        $document->setPaiement($paiement);

        $em->merge($document);
        $em->flush();

        return $this->redirect($payment_url, 303);
    }

    /**
     * @Route("/paiement/validation/{token}", name="paiement_success")
     */
        public function paiementSuccess($token, DocumentService $documentService)
    {
        $document = $this->documentRepository->findOneBy(['token' => $token]);

        if(!$document){
            //pas de devis
            $this->addFlash('warning', 'Document inconnu!');
            return $this->redirectToRoute('accueil');

        }else if(!is_null($document->getNumeroFacture())){
            //document deja facturé
            $this->addFlash('warning', 'Document déjà payé!');
            return $this->redirectToRoute('accueil');

        }else if(is_null($document->getNumeroFacture())){

            \Payplug\Payplug::setSecretKey($_ENV["PAYPLUG_SECRET"]);
            $payment = \Payplug\Payment::retrieve($document->getPaiement()->getTokenTransaction());

            if($payment->is_paid){

                $payment_id = $payment->id;

                $payment_date = $this->getDateTimeImmutableFromTimestamp($payment->hosted_payment->paid_at);
                $card = $payment->card->brand.'(***** '.$payment->card->last4.' - '.$payment->card->exp_month.'/'.$payment->card->exp_year.')';
    
                //il faut creer le numero de facture
                $newNumero = $documentService->generateNewNumberOf('numeroFacture', 'getNumeroFacture');
                $document->setNumeroFacture($newNumero);
    
                $paiement = $this->paiementRepository->findOneBy(['tokenTransaction' => $payment_id]);
    
                $paiement->setTimeTransaction($payment_date)
                         ->setMoyenPaiement($card);
    
                $this->em->merge($paiement);
                $this->em->merge($document);
                $this->em->flush();

            }else{
                //document non payé
                $this->addFlash('warning', 'Erreur - document non payé!');
                return $this->redirectToRoute('accueil');
            }

        }

        return $this->render('site/paiement/success.html.twig', [
            'token' => $token,
            'informationsLegales' =>  $this->informationsLegalesRepository->findAll(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
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

    /**
     * @Route("/paiement/notificationUrl/{token}", name="paiement_notificationUrl")
     */
    public function notificationUrl($token)
    {

        \Payplug\Payplug::setSecretKey($_ENV["PAYPLUG_SECRET"]);
        $input = file_get_contents('php://input');

            $resource = \Payplug\Notification::treat($input);

            if ($resource instanceof \Payplug\Resource\Payment
                && $resource->is_paid) {
                $payment_id = $resource->id;
                $payment_date = $this->getDateTimeImmutableFromTimestamp($resource->hosted_payment->paid_at);
                $card = $resource->card->brand.'(***** '.$resource->card->last4.' - '.$resource->card->exp_month.'/'.$resource->card->exp_year.')';


                $paiement = $this->paiementRepository->findOneBy(['tokenTransaction' => $payment_id]);

                $paiement->setTimeTransaction($payment_date)
                ->setMoyenPaiement($card);

                $this->em->merge($paiement);
                $this->em->flush();
            }
    }


    private function explodeAdresse($adresse,$document){
        
        $adresseExploded = explode("<br/>", $adresse);
        
        $arrayAdresse = [];

        //si on a une association
        if(count($adresseExploded) > 4){
            $arrayAdresse['title'] = $adresseExploded[0]; //association
            $first_last = explode(" ", $adresseExploded[1]); // prénom et nom
            $arrayAdresse['first_name']  = $first_last[0];
            $arrayAdresse['last_name'] = $first_last[1];
            $arrayAdresse['email'] = $document->getUser()->getEmail();
            $arrayAdresse['adresse1'] = $adresseExploded[2];
            $postal_ville = explode(" ", $adresseExploded[3]);
            $arrayAdresse['postCode'] = $postal_ville[0];
            $arrayAdresse['city'] = $postal_ville[1];
            $arrayAdresse['country'] = $adresseExploded[4];
            $arrayAdresse['language'] = 'fr';
        }else{
            $arrayAdresse['title'] = "Mr / Mme";
            $first_last = explode(" ", $adresseExploded[0]); // prénom et nom
            $arrayAdresse['first_name']  = $first_last[0];
            $arrayAdresse['last_name'] = $first_last[1];
            $arrayAdresse['email'] = $document->getUser()->getEmail();
            $arrayAdresse['adresse1'] = $adresseExploded[1];
            $postal_ville = explode(" ", $adresseExploded[2]);
            $arrayAdresse['postCode'] = $postal_ville[0];
            $arrayAdresse['city'] = $postal_ville[1];
            $arrayAdresse['country'] = $adresseExploded[3];
            $arrayAdresse['language'] = 'fr';
        }

        return $arrayAdresse;
    }

    private function getDateTimeImmutableFromTimestamp($timestamp)
    {
        $tps = (int) $timestamp;
        $date = new DateTimeImmutable();

        return $date->setTimestamp($tps);
    }
}
