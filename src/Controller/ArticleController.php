<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Type;
use App\Form\ArticleFormType;
use App\Form\LoveFormType;
use App\Form\UserInfosFormType;
use App\Repository\ArticleRepository;
use App\Repository\TypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class ArticleController extends AbstractController
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/newArticle', name: 'newArticle', methods: ['GET', 'POST'])]
    public function newArticle(EntityManagerInterface $entityManager,Request $request, ArticleRepository $articleRepository){
        $article = new Article();
        $articleForm = $this->createForm(ArticleFormType::class, $article);
        $articleForm->handleRequest($request);
        if($articleForm->isSubmitted() && $articleForm->isValid()){
            $article
                ->setCreationDate(new \DateTime('now'))
                ->setUpdateDate(new \DateTime('now'))
                ->setActive(true)
                ->setUser($this->getUser());
            $articleRepository->add($article);
            return $this->redirectToRoute('articleList');
        }
        return $this->render('article/article.html.twig', [
            'article' => $articleForm->createView()
        ]);
    }

    #[Route('/articleList', name: 'articleList', methods: ['GET', 'POST'])]
    public function articleList (ArticleRepository $articleRepository, UserRepository $userRepository){
        $article = $articleRepository->findAll();

        return $this->render('article/articleShow.html.twig', [
            'articles' => $article
        ]);
    }

    #[Route('/updateArticle/{id}', name: 'updateArticle', methods: ['GET', 'POST'])]
    public function updateArticle(ArticleRepository $articleRepository, $id, Request $request){
        $article = $articleRepository->findOneBy(['id' => $id]);
        $articleForm = $this->createForm(ArticleFormType::class, $article);
        $articleForm->handleRequest($request);
        if($articleForm->isSubmitted() && $articleForm->isValid()){
            $article
                ->setUpdateDate(new \DateTime('now'))
                ->setActive(true);
            $articleRepository->add($article);
            return $this->redirectToRoute('articleList');
        }
        return $this->render('article/updateArticle.html.twig', [
            'articleForm' => $articleForm->createView(),
            'article' => $article
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/deleteArticle/{id}', name: 'deleteArticle', methods: ['GET', 'POST'])]
    public function deleteArticle(ArticleRepository $articleRepository, $id): Response{
        $article = $articleRepository->findOneBy(['id' => $id]);
        $articleRepository->remove($article);

        return $this->redirectToRoute('articleList');
    }

    #[Route('/love/{id}', name: 'love', methods: ['GET', 'POST'])]
    public function love(ArticleRepository $articleRepository, $id){
        $article = $articleRepository->findOneBy(['id' => $id]);

        //dans $article->récupère ma table de jointure->regarde si user connecté est contenu dans cette table
        if($article->getLove()->contains($this->getUser()) == true){
            $article->removeLove($this->getUser());
        } else{
            $article->addLove($this->getUser());
        }
        $articleRepository->add($article);

        return $this->redirectToRoute('articleList');
    }
}