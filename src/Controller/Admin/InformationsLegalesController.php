<?php

namespace App\Controller\Admin;

use App\Entity\InformationsLegales;
use App\Form\Site\InformationsLegalesType;
use App\Repository\InformationsLegalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/informations-legales", name="admin_")
 */
class InformationsLegalesController extends AbstractController
{
    /**
     * @Route("/", name="informations_legales_index", methods={"GET"})
     */
    public function index(InformationsLegalesRepository $informationsLegalesRepository): Response
    {
        return $this->render('admin/informations_legales/index.html.twig', [
            'informations_legales' => $informationsLegalesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="informations_legales_new", methods={"GET", "POST"})
     */
    public function new(Request $request, InformationsLegalesRepository $informationsLegalesRepository): Response
    {
        $informationsLegale = new InformationsLegales();
        $form = $this->createForm(InformationsLegalesType::class, $informationsLegale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $informationsLegalesRepository->add($informationsLegale);
            return $this->redirectToRoute('admin_informations_legales_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/informations_legales/new.html.twig', [
            'informations_legale' => $informationsLegale,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="informations_legales_show", methods={"GET"})
     */
    public function show(InformationsLegales $informationsLegale): Response
    {
        return $this->render('informations_legales/show.html.twig', [
            'informations_legale' => $informationsLegale,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="informations_legales_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, InformationsLegales $informationsLegale, InformationsLegalesRepository $informationsLegalesRepository): Response
    {
        $form = $this->createForm(InformationsLegalesType::class, $informationsLegale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $informationsLegalesRepository->add($informationsLegale);
            return $this->redirectToRoute('admin_informations_legales_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/informations_legales/edit.html.twig', [
            'informations_legale' => $informationsLegale,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="informations_legales_delete", methods={"POST"})
     */
    public function delete(Request $request, InformationsLegales $informationsLegale, InformationsLegalesRepository $informationsLegalesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$informationsLegale->getId(), $request->request->get('_token'))) {
            $informationsLegalesRepository->remove($informationsLegale);
        }

        return $this->redirectToRoute('informations_legales_index', [], Response::HTTP_SEE_OTHER);
    }
}
