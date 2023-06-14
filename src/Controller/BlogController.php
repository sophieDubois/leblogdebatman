<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Form\NewPublicationFormType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/*prefixe de la route et du nom de toutes les pages de la partie blog du site*/
#[Route('/blog',name:'blog_')]

class BlogController extends AbstractController
{


    /*controleur qui creer un nouvel article*/
    #[Route('/nouvelle-publication/', name: 'publication_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function publicationNew(Request $request, ManagerRegistry $doctrine): Response
    {
        //création d'un nouvelle article
        $newArticle = new Article();

        //creation d'un formulaire de creation d'article lié a l'article vide
        $form = $this->createForm(NewPublicationFormType::class, $newArticle);

        //liaison des données POST au formulaire
        $form->handleRequest($request);

        //si le formulaire a été envoié sans erreures
        if ($form->isSubmitted() && $form->isValid()){

            //on termine hydrater l'article
            $newArticle
                ->setPublicationDate(new \DateTime())
                ->setAuthor($this->getUser())
                ;

            //sauvegarde bdd grace au manager des entités
            $em = $doctrine->getManager();
            $em->persist($newArticle);
            $em->flush();

            //message flash de succes
            $this->addFlash('success', 'Article publié avec succès !');

            //redirige sur la page qui montre le new article
            return $this->redirectToRoute('blog_publication_view', [
                'slug' => $newArticle->getSlug(),
            ]);

        }
        dump($newArticle);


        return $this->render('blog/publication_new.html.twig', [
            'new_publication_form' => $form->createView(),
        ]);

    }

    /*controleur de la page liste les articles*/
    #[Route('/publications/liste/', name: 'publication_list')]
    public function publicationList(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {
        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT a FROM App\Entity\Article a ORDER BY a.publicationDate DESC');

        $articles =$paginator->paginate(
            $query,
            $requestedPage,
            10
        );



        return $this->render('blog/publication_list.html.twig',[
            'articles' => $articles,
        ]);
    }





    /*controlleur pour voir un article en detail*/
    #[Route('/publication/{slug}/', name: 'publication_view')]
    public function publicationView(Article $article, Request $request, ManagerRegistry $doctrine): Response
    {
        if (!$this->getUser()){
            return $this->render('blog/publication_view.html.twig',[
                'article'=>$article,
            ]);
        }


        $comment = new Comment();

        $form = $this->createForm(CommentFormType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $comment
                ->setPublicationDate(new \DateTime())
                ->setAuthor( $this->getUser())
                ->setArticle($article)
                ;

            $em = $doctrine->getManager();
            $em ->persist($comment);
            $em->flush();

            unset($comment);
            unset($form);

            $comment = new Comment();
            $form = $this->createForm(CommentFormType::class, $comment);

            $this->addFlash('success', 'Votre commentaire a été publié avec succès !');
        }




        return $this->render('blog/publication_view.html.twig',[
            'article'=>$article,
            'comment_create_form' => $form->createView(),
        ]);



    }

    /*controleur de la page admin servant à supprimer un article via son id passé ds l'URL
    acces reservé aux administrateurs (ROLE-ADMIN)*/
    #[Route('/publication/suppression/{id}/', name: "publication_delete", priority: 10)]
    #[IsGranted('ROLE_ADMIN')]
    public function publicationDelete(Article $article, ManagerRegistry $doctrine, Request $request): Response
    {

        //verif token csrf valide
        if (!$this->isCsrfTokenValid('blog_publication_delete_' . $article->getId(), $request->query->get('csrf_token'))){

            $this->addFlash('error', 'Token sécurité invalide, veuillez ré-essayer.');

        }else{

            $em = $doctrine->getManager();
            $em->remove($article);
            $em->flush();

            $this->addFlash('success', 'la publication a été supprimée avec succès !');

    }

        return $this->redirectToRoute('blog_publication_list');

    }

    /*controleur de la page admin servant a modifier un article existant via son id passé ds l'URL
    acces reservé aux administrateur (ROLE_ADMIN)*/
    #[Route('/publication/modifier/{id}/', name: 'publication_edit', priority: 10)]
    #[IsGranted('ROLE_ADMIN')]
    public function publicationEdit(Article $article, Request $request, ManagerRegistry $doctrine):Response
    {
        $form = $this->createForm(NewPublicationFormType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('success', 'Publication modifiéé avec succès !');

            return $this->redirectToRoute('blog_publication_view', [
                'slug' => $article->getSlug(),
            ]);
        }

        return $this->render('blog/publication_edit.html.twig', [
            'edit_form' => $form->createView(),
        ]);
    }

}
