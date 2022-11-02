<?php

namespace App\Controller\Admin;

use App\Entity\Occasion;
use App\Form\OccasionStatutChangeType;
use App\Form\OccasionType;
use App\Repository\BoiteRepository;
use App\Repository\OccasionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin/occasion", name="admin_")
 */
class OccasionController extends AbstractController
{
    /**
     * @Route("/", name="occasion_index", methods={"GET"})
     */
    public function index(BoiteRepository $boiteRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $donnees = $boiteRepository->findBy(['isComplet' => true],['nom' => "ASC"]);

        $boites = $paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            25 /*limit per page*/
        );

        $images = [];

        // foreach ($boites as $key => $boite) {
        //     $images[$key] = stream_get_contents($boite->getImageBlob());
        // }

        return $this->render('admin/occasion/index.html.twig', [
            'boites' => $boites,
            'images'    => $images
        ]);
    }

    /**
     * @Route("/{id}/{slug}", name="occasion_liste", methods={"GET"})
     */
    public function occasionListeParBoite(
        BoiteRepository $boiteRepository,
        OccasionRepository $occasionRepository,
        $id,
        $slug): Response
    {

        $boite = $boiteRepository->find($id);
        $imageBoite = $boite->getImageBlob();

        $occasions = $occasionRepository->findBy(['boite' => $id], ['reference' => 'ASC']);

        // $images = [];

        // foreach ($occasions as $key => $occasion) {
        //     $images[$key] = stream_get_contents($occasion->getBoite()->getImageBlob());
        // }

        return $this->render('admin/occasion/liste_par_boite.html.twig', [
            'boite'     => $boite,
            'image'     => $imageBoite,
            'occasions' => $occasions,
            // 'images'    => $images
        ]);
    }

    /**
     * @Route("/creation/{boite}/new/", name="occasion_new", methods={"GET", "POST"})
     */
    public function new(Request $request, OccasionRepository $occasionRepository, BoiteRepository $boiteRepository, $boite, SluggerInterface $sluggerInterface): Response
    {
        $boite = $boiteRepository->find($boite);

        $occasion = new Occasion();
        $form = $this->createForm(OccasionType::class, $occasion);
        $form->handleRequest($request);

        $occasion->setBoite($boite)
                 ->setDonation(false)
                 ->setSale(false);

        if ($form->isSubmitted() && $form->isValid()) {
            $occasionRepository->add($occasion);

            $lastId = $occasion->getId();
            $occasion->setReference($boite->getId().'-'.$lastId);
            $occasionRepository->add($occasion);

            return $this->redirectToRoute('admin_occasion_liste', ['id' => $boite->getId(), 'slug' => $sluggerInterface->slug($boite->getNom())], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/occasion/new.html.twig', [
            'occasion' => $occasion,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="occasion_show", methods={"GET"})
     */
    public function show(Occasion $occasion): Response
    {
        return $this->render('admin/occasion/show.html.twig', [
            'occasion' => $occasion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="occasion_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Occasion $occasion, OccasionRepository $occasionRepository): Response
    {
        $form = $this->createForm(OccasionType::class, $occasion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $occasionRepository->add($occasion);
            return $this->redirectToRoute('admin_occasion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/occasion/edit.html.twig', [
            'occasion' => $occasion,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="occasion_delete", methods={"POST"})
     */
    public function delete(Request $request, Occasion $occasion, OccasionRepository $occasionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$occasion->getId(), $request->request->get('_token'))) {
            $occasionRepository->remove($occasion);
        }

        return $this->redirectToRoute('admin_occasion_index', [], Response::HTTP_SEE_OTHER);
    }
}
