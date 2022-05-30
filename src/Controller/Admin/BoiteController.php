<?php

namespace App\Controller\Admin;

use App\Entity\Boite;
use App\Form\BoiteType;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/boite", name="admin_")
 */
class BoiteController extends AbstractController
{
    /**
     * @Route("/", name="boite_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $donnees = $entityManager
            ->getRepository(Boite::class)
            ->findBy([], ['nom' => "ASC"]);

        $boites = $paginator->paginate(
            $donnees, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            25 /*limit per page*/
        );
        //on va stocker les images
        $images = [];
        foreach ($boites as $key => $boite) {
            $images[$key] = stream_get_contents($boite->getImageBlob());
        }

        return $this->render('admin/boite/index.html.twig', [
            'boites' => $boites,
            'images' => $images
        ]);
    }

    /**
     * @Route("/new", name="boite_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $boite = new Boite();
        $form = $this->createForm(BoiteType::class, $boite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($boite);
            $entityManager->flush();

            return $this->redirectToRoute('boite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/boite/new.html.twig', [
            'boite' => $boite,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="boite_show", methods={"GET"})
     */
    public function show(Boite $boite): Response
    {
        return $this->render('admin/boite/show.html.twig', [
            'boite' => $boite,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="boite_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Boite $boite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BoiteType::class, $boite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_boite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/boite/edit.html.twig', [
            'boite' => $boite,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="boite_delete", methods={"POST"})
     */
    public function delete(Request $request, Boite $boite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$boite->getId(), $request->request->get('_token'))) {
            $entityManager->remove($boite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('boite_index', [], Response::HTTP_SEE_OTHER);
    }
}
