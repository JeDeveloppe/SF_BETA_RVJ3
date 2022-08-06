<?php

namespace App\Controller\Site;

use App\Entity\InformationsLegales;
use App\Form\ContactType;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(InformationsLegalesRepository $informationsLegalesRepository): Response
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
            'informationsLegales' =>  $informationsLegalesRepository->findAll()
        ]);
    }

    /**
     * @Route("/conditions-generale-de-vente", name="cgv")
     */
    public function cgv(InformationsLegalesRepository $informationsLegalesRepository): Response
    {

        return $this->render('site/informations/cgv.html.twig', [
            'informationsLegales' =>  $informationsLegalesRepository->findAll()
        ]);
    }

    /**
     * @Route("/mentions-legales", name="mentions-legales")
     */
    public function mentionsLegales(InformationsLegalesRepository $informationsLegalesRepository): Response
    {

        return $this->render('site/informations/mentions_legales.html.twig', [
            'informationsLegales' =>  $informationsLegalesRepository->findAll()
        ]);
    }

    /**
     * @Route("/nous-soutenir", name="nous-soutenir")
     */
    public function nousSoutenir(InformationsLegalesRepository $informationsLegalesRepository): Response
    {

        return $this->render('site/informations/nous_soutenir.html.twig', [
            'informationsLegales' =>  $informationsLegalesRepository->findAll()
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(
        InformationsLegalesRepository $informationsLegalesRepository,
        Request $request,
        MailerService $mailerService
        ): Response
    {

        $informationsLegales = $informationsLegalesRepository->findAll();

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
            return $this->redirectToRoute('contact', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('site/contact.html.twig', [
            'informationsLegales' =>  $informationsLegales,
            'form' => $form->createView()
        ]);
    }
}
