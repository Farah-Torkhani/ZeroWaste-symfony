<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;

class FrontController extends AbstractController
{
    #[Route('/base', name: 'app_front')]
    public function index(ManagerRegistry $doct): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        
        return $this->render('front/frontBase.html.twig', [
            'controller_name' => 'FrontController',
            'title' => 'Zero Waste',
            'user' => $user,
        ]);
    }



}
