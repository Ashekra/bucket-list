<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_home")
     */
    public function index(): Response
    {
        return $this->render('main/home.html.twig');
    }

    /**
     * @Route("/about-us", name="about_us")
     */
    public function aboutUs(): Response
    {
        return $this->render('main/about.html.twig');
    }

    /**
     * @Route("/legal-stuff", name="legal_stuff")
     */
    public function legalStuff(): Response
    {
        return $this->render('main/legal.html.twig');
    }
}
