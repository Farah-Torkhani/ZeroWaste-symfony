<?php

namespace App\Controller;

use App\Entity\DonHistory;
use App\Entity\User;
use App\Entity\Fundrising;
use App\Form\DonHistoryType;
use App\Repository\DonHistoryRepository;
use App\Repository\FundrisingRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Stripe\Charge;
use Stripe\Stripe;

#[Route('/don/history')]
class DonHistoryController extends AbstractController
{
    #[Route('/', name: 'app_don_history_index', methods: ['GET'])]
    public function index(DonHistoryRepository $donHistoryRepository, ManagerRegistry $doct): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        return $this->render('don_history/index.html.twig', [
            'don_histories' => $donHistoryRepository->findAll(),
        ]);
    }

    #[Route('/afficherFundsdetails/{id}', name: 'afficherFundsdetails')]
    public function afficherfundsdetails(Request $request, Fundrising $fund, UserRepository $userRepo): Response
    {

        $user = $userRepo->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        

        return $this->render('don_history/afficherDon.html.twig');

    }

    #[Route('/new/{id}', name: 'app_don_history_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DonHistoryRepository $donHistoryRepository, ManagerRegistry $doct, FundrisingRepository $fundrisingRepository): Response
    {
        $fundrisingId = $request->get('id');
        $fundrising = $doct->getRepository(Fundrising::class)->findOneBy(['id' => $fundrisingId]);
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $donHistory = new DonHistory();
        $form = $this->createForm(DonHistoryType::class, $donHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donHistory->setUserID($user);
            $donHistory->setFundsID($fundrising);
            $donHistory->setDateDonation(new \Datetime);
            $donHistoryRepository->save($donHistory, true);
            $fundrisingRepository->sms();


           // dd("tttttttttt");
            //return $this->redirectToRoute('afficherFundsdetails/{id}', [], Response::HTTP_SEE_OTHER);
           return $this->redirectToRoute('afficherFundsdetail', array('id' =>$fundrisingId ));
        }

        return $this->renderForm('don_history/new.html.twig', [
            'don_history' => $donHistory,
            'form' => $form,
            'user' => $user,
        ]);
    }
    

    #[Route('/afficherDon', name: 'app_don_history_show', methods: ['GET'])]
    public function afficher(DonHistory $donHistory): Response
    
    {
        return $this->render('don_history/show.html.twig', [
            'don_history' => $donHistory,
        ]);
    }

    #[Route('/{id}', name: 'app_don_history_show', methods: ['GET'])]
    public function show(DonHistory $donHistory): Response
    {
        return $this->render('don_history/show.html.twig', [
            'don_history' => $donHistory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_don_history_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DonHistory $donHistory, DonHistoryRepository $donHistoryRepository): Response
    {
        $form = $this->createForm(DonHistoryType::class, $donHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donHistoryRepository->save($donHistory, true);

            return $this->redirectToRoute('app_don_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('don_history/edit.html.twig', [
            'don_history' => $donHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_don_history_delete', methods: ['POST'])]
    public function delete(Request $request, DonHistory $donHistory, DonHistoryRepository $donHistoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$donHistory->getId(), $request->request->get('_token'))) {
            $donHistoryRepository->remove($donHistory, true);
        }

        return $this->redirectToRoute('app_don_history_index', [], Response::HTTP_SEE_OTHER);
    }

    //payment stripe
    #[Route('/payment1', name: 'app_payment1')]
    
    public function paymentindex(): Response
    {
        return $this->render('don_history/afficherDon.html.twig', [
            'controller_name' => 'PaymentController',
            'stripe_key' => $_ENV["STRIPE_PUBLIC_KEY"],
        ]);
    }

    #[Route('/payment/create-charge1', name: 'app_stripe_charge1', methods: ['POST'])]
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
        return $this->redirectToRoute('app_payment1', [], Response::HTTP_SEE_OTHER);
    }
}
