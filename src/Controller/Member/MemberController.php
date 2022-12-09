<?php

namespace App\Controller\Member;

use App\Form\UserType;
use App\Service\DocumentService;
use App\Repository\UserRepository;
use App\Repository\PanierRepository;
use App\Repository\AdresseRepository;
use App\Repository\DocumentRepository;
use App\Repository\ConfigurationRepository;
use App\Repository\DocumentLignesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use App\Service\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MemberController extends AbstractController
{

    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentLignesRepository $documentLignesRepository,
        private InformationsLegalesRepository $informationsLegalesRepository,
        private Utilities $utilities,
        private Security $security,
        private PanierRepository $panierRepository)
    {
    }


    /**
     * @Route("/membre", name="app_member")
     */
    public function index(): Response
    {
        return $this->render('member/index.html.twig', [
            'controller_name' => 'MemberController',
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/membre/adresses", name="app_member_adresses", methods={"GET"})
     */
    public function membreAdresses(
        AdresseRepository $adresseRepository,
        ): Response
    {

        $user = $this->security->getUser();

        // dd($adresseRepository->findBy(['user' => $user, 'isFacturation' => true]));
        return $this->render('member/adresse/index.html.twig', [
            'livraison_adresses' => $adresseRepository->findBy(['user' => $user, 'isFacturation' => null]),
            'facturation_adresses' => $adresseRepository->findBy(['user' => $user, 'isFacturation' => true]),
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);

    }

    /**
     * @Route("/membre/historique", name="app_member_historique")
     */
    public function membreHistorique(
        DocumentRepository $documentRepository,
        ConfigurationRepository $configurationRepository): Response
    {
        $user = $this->security->getUser();

        //on cherche les devis
        $devis = $documentRepository->findDevisFromUser($user);
        //on cherche les factures
        $factures = $documentRepository->findFacturesFromUser($user);

        //on met tout dans le meme tableau...
        $documents = array_merge($devis,$factures);
        //...que l'on tri par ordre décroissant d'id
        rsort($documents);

        $configurations = $configurationRepository->findAll();
        $configuration = $configurations[0];

        return $this->render('member/historique.html.twig', [
            'user' => $user,
            'documents' => $documents,
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/membre/mon-compte", name="app_member_compte")
     */
    public function membreCompte(
        Request $request,
        UserRepository $userRepository,): Response
    {
        $user = $this->security->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user);
            return $this->redirectToRoute('app_member_compte', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('member/compte.html.twig', [
            'controller_name' => 'MemberController',
            'form' => $form->createView(),
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/membre/download/facture/{token}", name="app_member_facture_download")
     */
    public function factureDownload($token, DocumentService $documentService)
    {
        $documentService->factureToPdf($token);

        return new Response();
    }
}
