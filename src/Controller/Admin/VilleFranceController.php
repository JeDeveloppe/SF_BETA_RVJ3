<?php

namespace App\Controller\Admin;

use App\Entity\VilleFrance;
use App\Form\VilleFranceType;
use App\Repository\VilleFranceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/villes/france")
 */
class VilleFranceController extends AbstractController
{
    /**
     * @Route("/", name="app_ville_france_index", methods={"GET"})
     */
    public function index(VilleFranceRepository $villeFranceRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $donnees = $villeFranceRepository->findBy([], ['villeNom' => 'ASC']);

        $villes = $paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            100 /*limit per page*/
        );

        return $this->render('admin/ville_france/index.html.twig', [
            'ville_frances' => $villes,
        ]);
    }

    /**
     * @Route("/new", name="app_ville_france_new", methods={"GET", "POST"})
     */
    public function new(Request $request, VilleFranceRepository $villeFranceRepository): Response
    {
        $villeFrance = new VilleFrance();
        $form = $this->createForm(VilleFranceType::class, $villeFrance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $villeFranceRepository->add($villeFrance);
            return $this->redirectToRoute('app_ville_france_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ville_france/new.html.twig', [
            'ville_france' => $villeFrance,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_ville_france_show", methods={"GET"})
     */
    public function show(VilleFrance $villeFrance): Response
    {
        return $this->render('ville_france/show.html.twig', [
            'ville_france' => $villeFrance,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_ville_france_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, VilleFrance $villeFrance, VilleFranceRepository $villeFranceRepository): Response
    {
        $form = $this->createForm(VilleFranceType::class, $villeFrance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $villeFranceRepository->add($villeFrance);
            return $this->redirectToRoute('app_ville_france_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/ville_france/edit.html.twig', [
            'ville_france' => $villeFrance,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_ville_france_delete", methods={"POST"})
     */
    public function delete(Request $request, VilleFrance $villeFrance, VilleFranceRepository $villeFranceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$villeFrance->getId(), $request->request->get('_token'))) {
            $villeFranceRepository->remove($villeFrance);
        }

        return $this->redirectToRoute('app_ville_france_index', [], Response::HTTP_SEE_OTHER);
    }
}
