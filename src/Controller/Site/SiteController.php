<?php

namespace App\Controller\Site;

use App\Form\Site\ContactType;
use App\Repository\BoiteRepository;
use App\Repository\ConfigurationRepository;
use App\Service\MailerService;
use App\Repository\PanierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use App\Repository\OccasionRepository;
use App\Repository\PartenaireRepository;
use App\Repository\PaysRepository;
use App\Service\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteController extends AbstractController
{

    public function __construct(
        private InformationsLegalesRepository $informationsLegalesRepository,
        private Security $security,
        private PanierRepository $panierRepository,
        private ConfigurationRepository $configurationRepository,
        private Utilities $utilities)
    {
    }

    /**
     * @Route("/", name="accueil")
     */
    public function index(
        PartenaireRepository $partenaireRepository,
        BoiteRepository $boiteRepository,
        PaysRepository $paysRepository,
        OccasionRepository $occasionRepository): Response
    {

        $lastEntries = $boiteRepository->findBy(['isOnLine' => true], ['createdAt' => 'DESC'], 8);
        //pays par DEFAULT
        $country = $paysRepository->findBy(['isoCode' => "FR"]);

        return $this->render('site/index.html.twig', [
            'boites' => $boiteRepository->findBy(['isOnLine' => true]),
            'occasions' => $occasionRepository->findBy(['isOnLine' => true]),
            'partenaires' => $partenaireRepository->findPartenairesFullVisibility($country),
            'controller_name' => 'SiteController',
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'lastEntries' => $lastEntries,
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/conditions-generale-de-vente", name="cgv")
     */
    public function cgv(): Response
    {

        return $this->render('site/informations/legale/cgv.html.twig', [
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier']),
        ]);
    }

    /**
     * @Route("/mentions-legales", name="mentions-legales")
     */
    public function mentionsLegales(): Response
    {

        return $this->render('site/informations/legale/mentions_legales.html.twig', [
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/nous-soutenir", name="nous-soutenir")
     */
    public function nousSoutenir(): Response
    {

        return $this->render('site/informations/aides/nous_soutenir.html.twig', [
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(
        Request $request,
        MailerService $mailerService
        ): Response
    {

        $informationsLegales = $this->informationsLegalesRepository->findOneBy([]);

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $mailerService->sendEmailContact(
                $informationsLegales->getAdresseMailSite(),
                $form->get('email')->getData(),
                "Message du site concernant: ".$form->get('sujet')->getData(),
                [
                    'expediteur' => $form->get('email')->getData(),
                    'message' => $form->get('message')->getData()
                ]
            );

            $this->addFlash('success', 'Message bien envoyé!');
            return $this->redirectToRoute('contact', [
                'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier']) 
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('site/contact.html.twig', [
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'form' => $form->createView(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/comment-ca-marche/", name="comment_ca_marche_passer_une_commande")
     */
    public function ccm_passer_cmd(): Response
    {

        return $this->render('site/informations/comment-ca-marche/passer-commande.html.twig', [
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/informations-legales/", name="informations-legales")
     */
    public function informations_legales(): Response
    {

        return $this->render('site/informations/legale/legale.html.twig', [
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }
    /**
     * @Route("/informations/comment-ca-marche", name="informations-comment-ca-marche")
     */
    public function informations_comment_ca_marche(): Response
    {

        return $this->render('site/informations/comment-ca-marche/ccm.html.twig', [
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }
}
