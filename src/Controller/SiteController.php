<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;

class SiteController extends AbstractController
{
    /**
     * @Route("/site", name="site")
     */
    public function index(articleRepository $repo)
    {
        $articles = $repo->findAll();
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('site/home.html.twig');
    }


    /**
     * @Route("/site/new", name="site_create")
     * @Route("/site/{id}/edit", name="site_edit")

     */
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager) {

        if(!$article) {
            $article = new Article();
        }

        // $form = $this->createFormBuilder($article)
        //              ->add('title')
        //              ->add('content')        
        //              ->add('image')
        //              ->getForm();

        $form = $this->createForm(ArticleType::class, $article);
            
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }
            
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('site_show',['id' => $article->getId()]);

        }

        return $this->render('site/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/site/{id}", name="site_show")
     */
    public function show(Article $article)
    {
        return $this->render('site/show.html.twig', [
            'article' => $article
        ]);
    }
}
