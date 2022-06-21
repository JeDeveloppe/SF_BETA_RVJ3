<?php

namespace App\Controller\Site;

use App\Repository\InformationsLegalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(): Response
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
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
}
