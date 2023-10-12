<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Attribute\isGranted;
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
    
    #[isGranted('ROLE_ADMIN')]
    
    public function creeArticle(EntityManagerInterface $entityManager): Response
    {   
        $article = new Article();
        $article->setTitre("Mon super premier article")
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
        

        return new Response($article->getTitre());
    }
}
