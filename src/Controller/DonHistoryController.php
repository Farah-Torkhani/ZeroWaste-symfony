<?php

namespace App\Controller;

use App\Entity\DonHistory;
use App\Form\DonHistoryType;
use App\Repository\DonHistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/don/history')]
class DonHistoryController extends AbstractController
{
    #[Route('/', name: 'app_don_history_index', methods: ['GET'])]
    public function index(DonHistoryRepository $donHistoryRepository): Response
    {
        return $this->render('don_history/index.html.twig', [
            'don_histories' => $donHistoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_don_history_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DonHistoryRepository $donHistoryRepository): Response
    {
        $donHistory = new DonHistory();
        $form = $this->createForm(DonHistoryType::class, $donHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donHistoryRepository->save($donHistory, true);

            return $this->redirectToRoute('app_don_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('don_history/new.html.twig', [
            'don_history' => $donHistory,
            'form' => $form,
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
}