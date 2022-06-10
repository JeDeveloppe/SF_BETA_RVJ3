<?php

namespace App\Controller\Admin;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/villes")
 */
class VilleController extends AbstractController
{
    /**
     * @Route("/", name="app_ville_index", methods={"GET"})
     */
    public function index(VilleRepository $villeRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $donnees = $villeRepository->findBy([], ['villeNom' => 'ASC']);

        $villes = $paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            100 /*limit per page*/
        );

        return $this->render('admin/ville/index.html.twig', [
            'villes' => $villes,
        ]);
    }

    /**
     * @Route("/new", name="app_ville_new", methods={"GET", "POST"})
     */
    public function new(Request $request, VilleRepository $villeRepository): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $villeRepository->add($ville);
            return $this->redirectToRoute('app_ville_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/ville/new.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_ville_show", methods={"GET"})
     */
    public function show(Ville $ville): Response
    {
        return $this->render('admin/ville/show.html.twig', [
            'ville' => $ville,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_ville_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Ville $ville, VilleRepository $villeRepository): Response
    {
        $form = $this->createForm(VilleType::class, $ville);
   
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $villeRepository->add($ville);
            return $this->redirectToRoute('app_ville_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/ville/edit.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_ville_delete", methods={"POST"})
     */
    public function delete(Request $request, Ville $ville, VilleRepository $villeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ville->getId(), $request->request->get('_token'))) {
            $villeRepository->remove($ville);
        }

        return $this->redirectToRoute('app_ville_index', [], Response::HTTP_SEE_OTHER);
    }
}
