<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CollectController extends AbstractController
{
    #[Route('/admin_collect', name: 'app_collect')]
    public function Admincollect(): Response
    {
        return $this->render('collect/dash-admin-collect.html.twig', [
            'controller_name' => 'CollectController',
            
        ]);
    }

    #[Route('/admin_participant', name: 'app_admin_participant')]
    public function Adminparticipant(): Response
    {
        return $this->render('collect/dash-admin-participants.html.twig', [
            'controller_name' => 'CollectController',
            
        ]);
    }

    #[Route('/user_collect', name: 'app_user_collect')]
    public function userCollect(): Response
    {
        return $this->render('collect/dash_user_collect.html.twig', [
            'controller_name' => 'CollectController',
            
        ]);
    }

    #[Route('/user_contact', name: 'app_contactp')]
    public function userContact(): Response
    {
        return $this->render('collect/dash_user_contact.html.twig', [
            'controller_name' => 'CollectController',
            
        ]);
    }

}
