<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashAdminController extends AbstractController
{
    #[Route('/dash/admin/base', name: 'app_dash_admin_base')]
    public function index(): Response
    {
        return $this->render('dash_admin/dashAdminBase.html.twig', [
            'controller_name' => 'DashAdminController',
            'title' => 'ZeroWaste-dash',
        ]);
    }


    // #[Route('/dash/admin/home', name: 'app_dash_admin_home')]
    // public function dashAdminHome(): Response
    // {
    //     $userFullname = "Braiek Ali";

    //     return $this->render('dash_admin/dash-admin-home.html.twig', [
    //         'title' => 'Zero Waste',
    //         'userFullname' => $userFullname,
    //     ]);
    // }




}