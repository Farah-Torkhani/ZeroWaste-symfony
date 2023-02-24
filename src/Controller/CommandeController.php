<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommandesRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Commandes;
use App\Entity\Produit;
use DateTime;


class CommandeController extends AbstractController
{
    
    #[Route('/commands', name: 'app_commands')]
    public function commands(CommandesRepository $commandesRepository, ManagerRegistry $doct): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        
        $totalCommandes = $commandesRepository->getCommandesNumber($user->getId());
       

        $commandes = $commandesRepository->findBy(["user_id" => $user->getId(), "checkOut" => 0]);
        return $this->render('front/front-user-commands.html.twig', [
            'title' => 'Zero Waste',
            'commandes' => $commandes,
            'user' => $user,
            'totalCommandes' => $totalCommandes,
        ]);
    }

    #[Route('/commands/plus/{id}', name: 'app_commands-plus')]
    public function commandsPlus(CommandesRepository $commandesRepository, ManagerRegistry $doct, $id): Response
    {

        $commande = $commandesRepository->findOneBy(['id' => $id]);
        $quantite = $commande->getQuantiteC();

        if($quantite < $commande->getProduitId()->getQuantite()){
            $commande->setQuantiteC($quantite + 1);
            $em = $doct->getManager();
            $em->flush();
        }

        return $this->redirectToRoute('app_commands');
    }

    #[Route('/commands/moins/{id}', name: 'app_commands-moins')]
    public function commandsMoins(CommandesRepository $commandesRepository, ManagerRegistry $doct, $id): Response
    {

        $commande = $commandesRepository->findOneBy(['id' => $id]);
        $quantite = $commande->getQuantiteC();

        if($quantite > 1){
            $commande->setQuantiteC($quantite - 1);
            $em = $doct->getManager();
            $em->flush();
        }

        return $this->redirectToRoute('app_commands');
    }

    #[Route('/commands/delete/{id}', name: 'app_commands-delete')]
    public function commandsDelete(ManagerRegistry $repo, $id): Response
    {

        $commande = $repo->getRepository(Commandes::class)->find($id);
        $em = $repo->getManager();
        $em->remove($commande);
        $em->flush();

        return $this->redirectToRoute('app_commands');
    }

    #[Route('/commandes/add/{id}', name: 'app_commandes-add')]
    public function commandesAdd(CommandesRepository $commandesRepository, ManagerRegistry $doct, $id): Response
    {

        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $commandeFound = $commandesRepository->findBy(["user_id" => $user->getId(), "checkOut" => 0, "produit_id" => $id]);
        if($commandeFound != null){
            return $this->redirectToRoute('app_products', ['added' => 2]);
        }
        
        
        $produit = $doct->getRepository(Produit::class)->find($id);

        $commande = new Commandes();

        $commande->setProduitId($produit);
        $commande->setUserId($user);
        $commande->setCheckOut(0);
        $commande->setQuantiteC(1);
        $commande->setDateAjout(new DateTime());
        
        $em = $doct->getManager();
        $em->persist($commande);
        $em->flush();

        return $this->redirectToRoute('app_products', ['added' => 1]);

    }

}
