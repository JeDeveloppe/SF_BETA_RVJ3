<?php

namespace App\Controller\Admin;

use App\Entity\VilleBelgique;
use App\Form\VilleBelgiqueType;
use App\Repository\VilleBelgiqueRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("admin/villes/belgique")
 */
class VilleBelgiqueController extends AbstractController
{
    /**
     * @Route("/", name="app_ville_belgique_index", methods={"GET"})
     */
    public function index(VilleBelgiqueRepository $villeBelgiqueRepository, Request $request,  PaginatorInterface $paginator): Response
    {
        $donnees = $villeBelgiqueRepository->findBy([], ['villeNom' => 'ASC']);

        $villes = $paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            100 /*limit per page*/
        );

        return $this->render('admin/ville_belgique/index.html.twig', [
            'ville_belgiques' => $villes,
        ]);
    }

    /**
     * @Route("/new", name="app_ville_belgique_new", methods={"GET", "POST"})
     */
    public function new(Request $request, VilleBelgiqueRepository $villeBelgiqueRepository): Response
    {
        $villeBelgique = new VilleBelgique();
        $form = $this->createForm(VilleBelgiqueType::class, $villeBelgique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $villeBelgiqueRepository->add($villeBelgique);
            return $this->redirectToRoute('app_ville_belgique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ville_belgique/new.html.twig', [
            'ville_belgique' => $villeBelgique,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_ville_belgique_show", methods={"GET"})
     */
    public function show(VilleBelgique $villeBelgique): Response
    {
        return $this->render('ville_belgique/show.html.twig', [
            'ville_belgique' => $villeBelgique,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_ville_belgique_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, VilleBelgique $villeBelgique, VilleBelgiqueRepository $villeBelgiqueRepository): Response
    {
        $form = $this->createForm(VilleBelgiqueType::class, $villeBelgique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $villeBelgiqueRepository->add($villeBelgique);
            return $this->redirectToRoute('app_ville_belgique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ville_belgique/edit.html.twig', [
            'ville_belgique' => $villeBelgique,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_ville_belgique_delete", methods={"POST"})
     */
    public function delete(Request $request, VilleBelgique $villeBelgique, VilleBelgiqueRepository $villeBelgiqueRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$villeBelgique->getId(), $request->request->get('_token'))) {
            $villeBelgiqueRepository->remove($villeBelgique);
        }

        return $this->redirectToRoute('app_ville_belgique_index', [], Response::HTTP_SEE_OTHER);
    }
}
