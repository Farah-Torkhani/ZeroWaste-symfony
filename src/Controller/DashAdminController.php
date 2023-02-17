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

    #[Route('/dash/admin/products', name: 'app_dash_admin_products')]
    public function dashProducts(): Response
    {
        $userFullname = "Braiek Ali";

        return $this->render('dash_admin/dash-admin-products.html.twig', [
            'title' => 'Zero Waste',
            'userFullname' => $userFullname,
        ]);
    }

    #[Route('/dash/admin/products/add', name: 'app_dash_admin_products_add')]
    public function dashProductsAdd(): Response
    {
        $userFullname = "Braiek Ali";

        return $this->render('dash_admin/dash-admin-product-add.html.twig', [
            'title' => 'Zero Waste',
            'userFullname' => $userFullname,
        ]);
    }

    #[Route('/dash/admin/home', name: 'app_dash_admin_home')]
    public function dashAdminHome(): Response
    {
        $userFullname = "Braiek Ali";

        return $this->render('dash_admin/dash-admin-home.html.twig', [
            'title' => 'Zero Waste',
            'userFullname' => $userFullname,
        ]);
    }

    #[Route('/dash/admin/commands', name: 'app_dash_admin_commands')]
    public function dashAdminCommands(): Response
    {
        $userFullname = "Braiek Ali";

        return $this->render('dash_admin/dash-admin-commands.html.twig', [
            'title' => 'Zero Waste',
            'userFullname' => $userFullname,
        ]);
    }

    // #[Route('/dash/admin/users', name: 'app_dash_admin_users')]
    // public function dashAdminUsers(): Response
    // {
    //     $userFullname = "Braiek Ali";

    //     return $this->render('dash_admin/dash-admin-users.html.twig', [
    //         'title' => 'Zero Waste',
    //         'userFullname' => $userFullname,
    //     ]);
    // }


}