<?php

namespace App\Controller\Admin;

use App\Entity\Envelope;
use App\Form\Admin\EnvelopeType;
use App\Repository\EnvelopeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/envelope')]
class EnvelopeController extends AbstractController
{
    #[Route('/', name: 'app_envelope_index', methods: ['GET'])]
    public function index(EnvelopeRepository $envelopeRepository): Response
    {
        return $this->render('admin/envelope/index.html.twig', [
            'envelopes' => $envelopeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_envelope_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnvelopeRepository $envelopeRepository): Response
    {
        $envelope = new Envelope();
        $form = $this->createForm(EnvelopeType::class, $envelope);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $envelopeRepository->add($envelope, true);

            return $this->redirectToRoute('app_envelope_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/envelope/new.html.twig', [
            'envelope' => $envelope,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_envelope_show', methods: ['GET'])]
    public function show(Envelope $envelope): Response
    {
        return $this->render('admin/envelope/show.html.twig', [
            'envelope' => $envelope,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_envelope_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Envelope $envelope, EnvelopeRepository $envelopeRepository): Response
    {
        $form = $this->createForm(EnvelopeType::class, $envelope);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $envelopeRepository->add($envelope, true);

            return $this->redirectToRoute('app_envelope_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/envelope/edit.html.twig', [
            'envelope' => $envelope,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_envelope_delete', methods: ['POST'])]
    public function delete(Request $request, Envelope $envelope, EnvelopeRepository $envelopeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$envelope->getId(), $request->request->get('_token'))) {
            $envelopeRepository->remove($envelope, true);
        }

        return $this->redirectToRoute('app_envelope_index', [], Response::HTTP_SEE_OTHER);
    }
}
