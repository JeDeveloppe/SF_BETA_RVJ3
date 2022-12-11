<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\AdminSearchUserType;
use App\Form\Admin\AdminUserType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/user')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET','POST'])]
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $form = $this->createForm(AdminSearchUserType::class);
        $form->handleRequest($request);

        //si on faite une recherche
        if(!is_null($form->get('recherche')->getData()) && strlen($form->get('recherche')->getData()) > 2){
            $recherche = str_replace(" ","%",$form->get('recherche')->getData());
            $donnees = $userRepository->findUsersInDatabase($recherche);

            $users = $paginator->paginate(
                $donnees, /* query NOT result */
                1, /*page number*/
                1000 /*limit per page*/
            );

            unset($form);
            $form = $this->createForm(AdminSearchUserType::class);
            
        }else{

            $donnees = $userRepository->findUsersByNameAsc();
            
            $users = $paginator->paginate(
                $donnees, /* query NOT result */
                1, /*page number*/
                25 /*limit per page*/
            );

        }

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'form' => $form->createView()
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
