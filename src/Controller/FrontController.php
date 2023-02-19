<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class FrontController extends AbstractController
{
    #[Route('/base', name: 'app_front')]
    public function index(): Response
    {
        return $this->render('front/frontBase.html.twig', [
            'controller_name' => 'FrontController',
            'title' => 'Zero Waste',
        ]);
    }

    #[Route('/commands', name: 'app_commands')]
    public function commands(): Response
    {
        return $this->render('front/front-user-commands.html.twig', [
            'controller_name' => 'FrontController',
            'title' => 'Zero Waste',
        ]);
    }

}
