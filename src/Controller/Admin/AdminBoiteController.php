<?php

namespace App\Controller\Admin;

use App\Entity\Boite;
use DateTimeImmutable;
use App\Form\BoiteType;
use App\Form\SearchBoiteType;
use App\Form\AdminSearchBoiteType;
use App\Repository\BoiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/boite", name="admin_")
 */
class AdminBoiteController extends AbstractController
{
    /**
     * @Route("/", name="boite_index", methods={"GET","POST"})
     */
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        PaginatorInterface $paginator,
        BoiteRepository $boiteRepository
        ): Response
    {
        $form = $this->createForm(AdminSearchBoiteType::class);
        $form->handleRequest($request);


        //si on faite une recherche
        if(!is_null($form->get('searchBoite')->getData())){
            $recherche = str_replace(" ","%",$form->get('searchBoite')->getData());
            $donnees = $boiteRepository->findBoiteInDatabase($recherche);

            $boites = $paginator->paginate(
                $donnees, /* query NOT result */
                1, /*page number*/
                50 /*limit per page*/
            );

        }else{
            $donnees = $entityManager
            ->getRepository(Boite::class)
            ->findBy([], ['nom' => "ASC"]);

            $boites = $paginator->paginate(
                $donnees, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                25 /*limit per page*/
            );
        }

        return $this->render('admin/boite/index.html.twig', [
            'boites' => $boites,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="boite_new", methods={"GET", "POST"})
     */
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        Security $security): Response
    {
        $boite = new Boite();
        $form = $this->createForm(BoiteType::class, $boite);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $user = $security->getUser();
            
            $imageSend = $form->get('imageblob')->getData();

            if(is_null($imageSend)){

                $this->addFlash('warning', 'L\'image de la boite est obligatoire !');
                
                return $this->redirectToRoute('admin_boite_new', [], Response::HTTP_SEE_OTHER);

            }else{
                
                $imageBase64 = base64_encode(file_get_contents($imageSend));
                $boite->setImageBlob($imageBase64);
            }

            if(is_null($form->get('slug')->getData())){
                $boite->setSlug($slugger->slug($boite->getNom()));
            }else{
                $boite->setSlug($slugger->slug($boite->getSlug()));
            }

            $boite->setCreatedAt( new DateTimeImmutable('now'));
            $boite->setCreator($user->getNickname());

            $entityManager->persist($boite);
            $entityManager->flush();

            return $this->redirectToRoute('admin_boite_index', [], Response::HTTP_SEE_OTHER);
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
    public function edit(Request $request, Boite $boite, EntityManagerInterface $entityManager, BoiteRepository $boiteRepository): Response
    {
        $form = $this->createForm(BoiteType::class, $boite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageSend = $form->get('imageblob')->getData();

            dd($imageSend);

            if(!is_null($imageSend)){
                $imageBase64 = base64_encode(file_get_contents($imageSend));
                $boite->setImageBlob($imageBase64);
            }else{
                
            }
            dd($boite);

            return $this->redirectToRoute('admin_boite_index', [], Response::HTTP_SEE_OTHER);
        }

        $image = $boite->getImageblob();

        return $this->renderForm('admin/boite/edit.html.twig', [
            'boite' => $boite,
            'form' => $form,
            'image' => $image
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
