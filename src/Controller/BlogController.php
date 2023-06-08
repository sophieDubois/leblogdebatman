<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\NewPublicationFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/*prefixe de la route et du nom de toutes les pages de la partie blog du site*/
#[Route('/blog',name:'blog_')]

class BlogController extends AbstractController
{


    /*controleur qui creer un nouvel article*/
    #[Route('/nouvelle-publication/', name: 'new_publication')]
    #[IsGranted('ROLE_ADMIN')]
    public function newPublication(Request $request, ManagerRegistry $doctrine): Response
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

            //TODO penser a rediriger sur la page qui montre le new article
            return $this->redirectToRoute('main_home');

        }
        dump($newArticle);


        return $this->render('blog/new_publication.html.twig', [
            'new_publication_form' => $form->createView(),
        ]);

    }

}