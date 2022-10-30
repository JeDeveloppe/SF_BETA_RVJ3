<?php

namespace App\Controller\Member;

use App\Entity\Adresse;
use App\Form\AdresseType;
use App\Repository\AdresseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/membre/adresses")
 */
class AdresseController extends AbstractController
{


    /**
     * @Route("/new/{slug}/", name="app_adresse_new", methods={"GET", "POST"})
     */
    public function new(
        $slug,
        Request $request,
        AdresseRepository $adresseRepository,
        Security $security,
        InformationsLegalesRepository $informationsLegalesRepository
        ): Response
    {
        $user = $security->getUser();
 
        $department = $user->getDepartment();

        $adresse = new Adresse();

        $adresse->setUser($user);

        $array = [];

        if($slug == "facturation"){
            $adresse->setIsFacturation(true);
            $array['titre'] = "facturation";
        }else if($slug == "livraison"){
            $adresse->setIsFacturation(false);
            $array['titre'] = "livraison";

        }else{
            $this->addFlash('danger', 'Url inconnue !');
            return $this->redirectToRoute('app_member_adresses', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(AdresseType::class, $adresse, ['department' => $department]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adresseRepository->add($adresse);
            $this->addFlash('success', 'Adresse créée !');
            return $this->redirectToRoute('app_member_adresses', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('member/adresse/new.html.twig', [
            'adresse' => $adresse,
            'form' => $form,
            'informationsLegales' =>  $informationsLegalesRepository->findAll(),
            'array' => $array
        ]);
    }

    /**
     * @Route("/{id}", name="app_adresse_show", methods={"GET"})
     */
    public function show(Adresse $adresse): Response
    {
        return $this->render('adresse/show.html.twig', [
            'adresse' => $adresse,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_adresse_edit", methods={"GET", "POST"})
     */
    public function edit(
        Request $request,
        Adresse $adresse,
        AdresseRepository $adresseRepository,
        Security $security,
        InformationsLegalesRepository $informationsLegalesRepository
        ): Response
    {
        $user = $security->getUser();
 
        $department = $user->getDepartment();

        $form = $this->createForm(AdresseType::class, $adresse, ['department' => $department]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adresseRepository->add($adresse);
            return $this->redirectToRoute('app_adresse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('member/adresse/edit.html.twig', [
            'adresse' => $adresse,
            'form' => $form,
            'informationsLegales' =>  $informationsLegalesRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="app_adresse_delete", methods={"POST"})
     */
    public function delete(Request $request, Adresse $adresse, AdresseRepository $adresseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adresse->getId(), $request->request->get('_token'))) {
            $adresseRepository->remove($adresse);
        }

        return $this->redirectToRoute('app_member_adresses', [], Response::HTTP_SEE_OTHER);
    }
}
