<?php

namespace App\Controller;

use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'app_payment1')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
            'stripe_key' => $_ENV["STRIPE_PUBLIC_KEY"],
        ]);
    }

    #[Route('/payment/create-charge', name: 'app_stripe_charge2', methods: ['POST'])]
    public function createCharge(Request $request)
    {
        Stripe::setApiKey($_ENV["STRIPE_SECRET_KEY"]);
        Charge::create ([
                "amount" => 5 * 100,
                "currency" => "usd",
                "source" => $request->request->get('stripeToken'),
                "description" => "Binaryboxtuts Payment Test"
        ]);
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        return $this->redirectToRoute('afficher_funds', [], Response::HTTP_SEE_OTHER);
    }
}
