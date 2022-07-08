<?php

namespace App\Controller\Site;

use App\Entity\Partenaire;
use App\Form\PartenaireType;
use App\Repository\PartenaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SitePartenaireController extends AbstractController
{
    /**
     * @Route("/partenaires/{pays}", name="partenaires", methods={"GET"})
     */
    public function index(PartenaireRepository $partenaireRepository, $pays): Response
    {
        $partenaires = $partenaireRepository->findBy(['pays' => $pays]);


        return $this->render('admin/partenaire/index.html.twig', [
            'partenaires' => $partenaires,
        ]);
    }
}
