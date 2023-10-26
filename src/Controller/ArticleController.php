<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\HttpFoundation\Test\Constraint\ResponseCookieValueSame;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Article;
use DateTimeImmutable; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    
    #[Route('/cree', name: "app_article_cree")]
    
    #[IsGranted('ROLE_ADMIN')]
    
    public function creeArticle(EntityManagerInterface $entityManager): Response
    {   
        $task = new Task();
        $task->setTask('Write a blog post');
        $task->setDueDate(new DateTimeImmutable());

        $form = $this->createFormBuilder($task)
        ->add('titre', TextType::class)
        ->getForm();

        $article = new Article();
        $article->setTitre($form->get('titre')->getData())
        ->setTexte("ouiouiouiouiouiouiouiouiouioui")
        ->setEtat(true)
        ->setDate(new DateTimeImmutable());
        // dd($article); Pour débug 

        // Dire à Doctrine que nous voulons éventuellement enregistrer l'article 
        $entityManager->persist($article);

        //  Exécute les queries 
        $entityManager->flush();

        return new Response("Saved new Article with id ".$article->getId()); 
    }

    #[Route('/voir/{id}', name: 'app_article_voir')]
    public function voirArticle(ArticleRepository $articleRepository, int $id): Response
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Aucun article avec cette id '. $id. ' ¯\_(ツ)_/¯');
        }

        return new Response("article trouvé:". $article->getTitre());
    }

    #[Route('/modifier/{id}', name: "app_article_modif")]
    public function modifArticle(ArticleRepository $articleRepository, int $id): Response
    {
        
    }

    #[Route('supprimer/{id}', name: 'app_article_supprimer')]
    public function supprimerArticle(ArticleRepository $articleRepository, int $id): Response
    {
        $article = $articleRepository->find($id);

        if ($article) {
            throw $this->createNotFoundException("L'article ". $id ." n'a pas été trouvé, et ne peut donc pas être supprimé, rip");
        }
        
        $articleRepository->remove($article);
        $articleRepository->flush();

        return new Response("Article supprimé."); 
    }
}
