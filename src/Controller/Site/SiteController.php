<?php

namespace App\Controller\Site;

use App\Form\ContactType;
use App\Service\MailerService;
use App\Repository\PanierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteController extends AbstractController
{

    public function __construct(
        private InformationsLegalesRepository $informationsLegalesRepository,
        private Security $security,
        private PanierRepository $panierRepository)
    {
    }

    /**
     * @Route("/", name="accueil")
     */
    public function index(): Response
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
            'informationsLegales' =>  $this->informationsLegalesRepository->findAll(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/conditions-generale-de-vente", name="cgv")
     */
    public function cgv(): Response
    {

        return $this->render('site/informations/cgv.html.twig', [
            'informationsLegales' =>  $this->informationsLegalesRepository->findAll(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/mentions-legales", name="mentions-legales")
     */
    public function mentionsLegales(): Response
    {

        return $this->render('site/informations/mentions_legales.html.twig', [
            'informationsLegales' =>  $this->informationsLegalesRepository->findAll(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }

    /**
     * @Route("/nous-soutenir", name="nous-soutenir")
     */
    public function nousSoutenir(): Response
    {

        return $this->render('site/informations/nous_soutenir.html.twig', [
            'informationsLegales' => $this->informationsLegalesRepository->findAll(),
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

        $informationsLegales = $this->informationsLegalesRepository->findAll();

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $mailerService->sendEmailContact(
                $informationsLegales[0]->getAdresseMailSite(),
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
            'informationsLegales' =>  $informationsLegales,
            'form' => $form->createView(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }
}
