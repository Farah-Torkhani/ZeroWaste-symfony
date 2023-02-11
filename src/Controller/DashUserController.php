<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashUserController extends AbstractController
{
    #[Route('/dash/user/base', name: 'app_dash_user_base')]
    public function baseDash(): Response
    {

        return $this->render('dash_user/dashUserBase.html.twig', [
            'controller_name' => 'DashUserController',
            'title' => 'ZeroWaste-dash',
        ]);
    }


    #[Route('/dash/user/home', name: 'app_dash_user_home')]
    public function dashUserHome(Request $request): Response
    {
        $userFullname = "Nabil Mersni";


        return $this->render('dash_user/dash-user-home.html.twig', [
            'title' => 'Zero Waste',
            'userFullname' => $userFullname,
        ]);
    }

    #[Route('/dash/user/funds', name: 'app_dash_user_funds')]
    public function dashUserFund(Request $request): Response
    {
        $userFullname = "Nabil Mersni";


        return $this->render('dash_user/dash-user-funds.html.twig', [
            'title' => 'Zero Waste',
            'userFullname' => $userFullname,
        ]);
    }
}
