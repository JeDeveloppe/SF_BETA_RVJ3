<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\Admin\AdminArticleType;
use App\Repository\ArticleRepository;
use App\Repository\BoiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/boite-{boite}/articles')]
class AdminArticleController extends AbstractController
{
    public function __construct(
        private BoiteRepository $boiteRepository
    )
    {
    }

    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository, $boite): Response
    {
        $boite = $this->boiteRepository->findOneBy(['id' => $boite]);
        $articles = $articleRepository->findArticlesFromBoiteInDatabase($boite);
 
        return $this->render('admin/article/index.html.twig', [
            'articles' => $articles,
            'boite' => $boite
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository, $boite): Response
    {
        $article = new Article();
        $form = $this->createForm(AdminArticleType::class, $article);
        $form->handleRequest($request);
        $boite = $this->boiteRepository->findOneBy(['id' => $boite]);


        if ($form->isSubmitted() && $form->isValid()) {
            $article->setReference('en_cours');
            $articleRepository->add($article, true);

            $article->setReference('RVJA_'.$boite->getId().'_'.$article->getId())->addBoite($boite);

            $articleRepository->add($article, true);

            return $this->redirectToRoute('app_article_index', ['boite' => $boite->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/article/new.html.twig', [
            'article' => $article,
            'form' => $form,
            'boite' => $boite
        ]);
    }

    // #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    // public function show(Article $article): Response
    // {
    //     return $this->render('admin/article/show.html.twig', [
    //         'article' => $article,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository, $boite): Response
    {
        $form = $this->createForm(AdminArticleType::class, $article);
        $form->handleRequest($request);
        $boite = $this->boiteRepository->findOneBy(['id' => $boite]);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->add($article, true);

            return $this->redirectToRoute('app_article_index', ['boite' => $boite->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
            'boite' => $boite
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
