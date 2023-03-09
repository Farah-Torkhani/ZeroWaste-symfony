<?php

namespace App\Controller;

use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CommandsRepository;
use App\Repository\AchatsRepository;
use App\Entity\User;

class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
            'stripe_key' => $_ENV["STRIPE_PUBLIC_KEY"],
        ]);
    }

    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request, ManagerRegistry $doct, CommandsRepository $commandsRepository, AchatsRepository $achatsRepository): Response
    {
        Stripe::setApiKey($_ENV["STRIPE_SECRET_KEY"]);
        Charge::create ([
                "amount" => 10 * 100,
                "currency" => "usd",
                "source" => $request->request->get('stripeToken'),
                "description" => "Binaryboxtuts Payment Test"
        ]);
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);
        $commandeAchats = $achatsRepository->findOneBy(["commande" => $commande]);
        
        $commandeAchats->setValidate(1);
        $commande->setStatus(1);
        $em = $doct->getManager();
        $em->flush();

        return $this->redirectToRoute('app_commands', [], Response::HTTP_SEE_OTHER);
    }
}