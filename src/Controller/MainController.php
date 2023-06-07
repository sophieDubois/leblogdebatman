<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MainController extends AbstractController
{
    /**
    *controleur de la page d'accueil
     **/


    #[Route('/', name: 'main_home')]
    public function home(): Response
    {
        return $this->render('main/home.html.twig');


    }

    /*controleur de la page de profil*/
    /*acces reservé aux personnes connectées*/
    #[Route('/mon-profil/', name: 'main-profil')]
    #[IsGranted('ROLE_USER')]
    public function profil(): Response
    {
        return $this->render('main/profil.html.twig');
    }
}
