<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/role')]
class AdminRoleController extends AbstractController
{
    #[Route('/', name: 'app_admin_role_index', methods: ['GET'])]
    public function index(RoleRepository $roleRepository): Response
    {
        return $this->render('admin_role/index.html.twig', [
            'roles' => $roleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_role_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RoleRepository $roleRepository): Response
    {
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roleRepository->add($role, true);

            return $this->redirectToRoute('app_admin_role_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_role/new.html.twig', [
            'role' => $role,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_role_show', methods: ['GET'])]
    public function show(Role $role): Response
    {
        return $this->render('admin_role/show.html.twig', [
            'role' => $role,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_role_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Role $role, RoleRepository $roleRepository): Response
    {
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roleRepository->add($role, true);

            return $this->redirectToRoute('app_admin_role_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_role/edit.html.twig', [
            'role' => $role,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_role_delete', methods: ['POST'])]
    public function delete(Request $request, Role $role, RoleRepository $roleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$role->getId(), $request->request->get('_token'))) {
            $roleRepository->remove($role, true);
        }

        return $this->redirectToRoute('app_admin_role_index', [], Response::HTTP_SEE_OTHER);
    }
}
