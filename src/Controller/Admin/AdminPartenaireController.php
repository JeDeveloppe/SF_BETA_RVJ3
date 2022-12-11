<?php

namespace App\Controller\Admin;

use App\Entity\Partenaire;
use App\Form\Admin\PartenaireType;
use App\Repository\PartenaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/partenaire")
 */
class AdminPartenaireController extends AbstractController
{
    /**
     * @Route("/", name="app_admin_partenaire_index", methods={"GET"})
     */
    public function index(PartenaireRepository $partenaireRepository): Response
    {

        $partenaires = $partenaireRepository->findBy([], ['name' => 'ASC']);

        return $this->render('admin/partenaire/index.html.twig', [
            'partenaires' => $partenaires,
        ]);
    }

    /**
     * @Route("/new", name="app_admin_partenaire_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PartenaireRepository $partenaireRepository): Response
    {
        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageSend = $form->get('imageBlob')->getData();
            $country = $form->get('ville')->getData()->getDepartement()->getPays();


            if($imageSend == null){
                $this->addFlash('danger', 'Image obligatoire!');
                return $this->redirect($request->headers->get('referer'));
            }else{
                $imageBase64 = base64_encode(file_get_contents($imageSend));
                $partenaire->setImageBlob($imageBase64);
            }

            $partenaire->setImageBlob($imageBase64)
                        ->setCountry($country);

            $partenaireRepository->add($partenaire);
            return $this->redirectToRoute('app_admin_partenaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/partenaire/new.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_admin_partenaire_show", methods={"GET"})
     */
    public function show(Partenaire $partenaire): Response
    {
        return $this->render('admin/partenaire/show.html.twig', [
            'partenaire' => $partenaire,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_admin_partenaire_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Partenaire $partenaire, PartenaireRepository $partenaireRepository): Response
    {
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageSend = $form->get('imageBlob')->getData();
            $country = $form->get('ville')->getData()->getDepartement()->getPays();

            if($imageSend != null){
                $imageBase64 = base64_encode(file_get_contents($imageSend));
                $partenaire->setImageBlob($imageBase64);
            }
            
            $partenaire->setCountry($country);
            $partenaireRepository->add($partenaire);
            return $this->redirectToRoute('app_admin_partenaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/partenaire/edit.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form
        ]);
    }

    /**
     * @Route("/{id}", name="app_admin_partenaire_delete", methods={"POST"})
     */
    public function delete(Request $request, Partenaire $partenaire, PartenaireRepository $partenaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$partenaire->getId(), $request->request->get('_token'))) {
            $partenaireRepository->remove($partenaire);
        }

        return $this->redirectToRoute('app_admin_partenaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
