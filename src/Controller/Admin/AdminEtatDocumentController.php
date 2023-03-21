<?php

namespace App\Controller\Admin;

use App\Entity\EtatDocument;
use App\Form\EtatDocumentType;
use App\Repository\EtatDocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/etat/document')]
class AdminEtatDocumentController extends AbstractController
{
    #[Route('/', name: 'app_etat_document_index', methods: ['GET'])]
    public function index(EtatDocumentRepository $etatDocumentRepository): Response
    {
        return $this->render('admin/etat_document/index.html.twig', [
            'etat_documents' => $etatDocumentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_etat_document_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EtatDocumentRepository $etatDocumentRepository): Response
    {
        $etatDocument = new EtatDocument();
        $form = $this->createForm(EtatDocumentType::class, $etatDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etatDocumentRepository->add($etatDocument, true);

            return $this->redirectToRoute('app_etat_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/etat_document/new.html.twig', [
            'etat_document' => $etatDocument,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etat_document_show', methods: ['GET'])]
    public function show(EtatDocument $etatDocument): Response
    {
        return $this->render('admin/etat_document/show.html.twig', [
            'etat_document' => $etatDocument,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etat_document_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EtatDocument $etatDocument, EtatDocumentRepository $etatDocumentRepository): Response
    {
        $form = $this->createForm(EtatDocumentType::class, $etatDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etatDocumentRepository->add($etatDocument, true);

            return $this->redirectToRoute('app_etat_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/etat_document/edit.html.twig', [
            'etat_document' => $etatDocument,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etat_document_delete', methods: ['POST'])]
    public function delete(Request $request, EtatDocument $etatDocument, EtatDocumentRepository $etatDocumentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etatDocument->getId(), $request->request->get('_token'))) {
            $etatDocumentRepository->remove($etatDocument, true);
        }

        return $this->redirectToRoute('app_etat_document_index', [], Response::HTTP_SEE_OTHER);
    }
}
