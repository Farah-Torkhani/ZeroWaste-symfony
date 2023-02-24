<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommandsRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CommandsProduitRepository;
use App\Entity\CommandsProduit;
use App\Entity\Produit;
use App\Entity\Commands;
use App\Entity\Achats;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AchatsType;
use App\Repository\AchatsRepository;
use DateTime;

class CommandsController extends AbstractController
{

    #[Route('/commands', name: 'app_commands')]
    public function commands(CommandsRepository $commandsRepository, AchatsRepository $achatsRepository, ManagerRegistry $doct, CommandsProduitRepository $commandsProduitRepository, Request $request): Response
    {

        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);
        $commandeAchats = $achatsRepository->findOneBy(["commande" => $commande]);
        if($commande != null)
        {
            $totalCommandes = $commandsProduitRepository->getCommandesNumber($commande->getId());
        }else{
            $totalCommandes = 0;
        }

        $achats = new Achats();
        $form = $this->createForm(AchatsType::class, $achats);
        $form->handleRequest($request);
        if ($form->isSubmitted() &&$form->isValid()) {
            $date = new DateTime();
            $achats->setDateAchat($date);
            $achats->setValidate(0);
            $achats->setCommande($commande);
            $em = $doct->getManager();
            
            $em->persist($achats);
            $em->flush();
            return $this->redirectToRoute("commands_checkout");
        }
    
        return $this->render('front/front-user-commands.html.twig', [
            'title' => 'Zero Waste',
            'commande' => $commande,
            'user' => $user,
            'totalCommandes' => $totalCommandes,
            "formAchats" => $form->createView(),
            "commandeAchats" => $commandeAchats,
        ]);
    }



    #[Route('/commands/plus/{id}', name: 'app_commands-plus')]
    public function commandsPlus(CommandsRepository $commandsRepository, CommandsProduitRepository $commandsProduitRepository, ManagerRegistry $doct, $id): Response
    {

        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);

        $commandeProduit = $commandsProduitRepository->findOneBy(["commande" => $commande->getId(), "produit" => $id]);
        
        $quantite = $commandeProduit->getQuantiteC();

        if($quantite < $commandeProduit->getProduit()->getQuantite()){
            $commandeProduit->setQuantiteC($quantite + 1);
            $em = $doct->getManager();
            $em->flush();
        }
    
        return $this->redirectToRoute('app_commands');
    }

    #[Route('/commands/moins/{id}', name: 'app_commands-moins')]
    public function commandsMoins(CommandsRepository $commandsRepository, CommandsProduitRepository $commandsProduitRepository, ManagerRegistry $doct, $id): Response
    {

        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);

        $commandeProduit = $commandsProduitRepository->findOneBy(["commande" => $commande->getId(), "produit" => $id]);
        
        $quantite = $commandeProduit->getQuantiteC();

        if($quantite > 1){
            $commandeProduit->setQuantiteC($quantite - 1);
            $em = $doct->getManager();
            $em->flush();
        }

        return $this->redirectToRoute('app_commands');
    }

    #[Route('/commands/delete/{id}', name: 'app_commands-delete')]
    public function commandsDelete(ManagerRegistry $repo, $id): Response
    {

        $commandeProduit = $repo->getRepository(CommandsProduit::class)->find($id);
        $em = $repo->getManager();
        $em->remove($commandeProduit);
        $em->flush();

        return $this->redirectToRoute('app_commands');
    }

    #[Route('/commandes/add/{id}', name: 'app_commandes-add')]
    public function commandesAdd(CommandsProduitRepository $commandsProduitRepository,CommandsRepository $commandsRepository, ManagerRegistry $doct, $id): Response
    {

        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);
        if($commande == null){
            //on ajoute une nouvelle commande puis on peut ajouter des produits à cette commande
            $commande = new Commands();
            $commande->setStatus(0);
            $commande->setUser($user);
            $em = $doct->getManager();
            $em->persist($commande);
            $em->flush();
        }

        //récuperer la commande courante
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);

        //ajouter un produit au commande
        //tester si le produit exite déjà dans la commande 
        $commandeProduit = $commandsProduitRepository->findBy(["commande" => $commande->getId(), "produit" => $id]);
        if($commandeProduit != null){
            return $this->redirectToRoute('app_products', ['added' => 2]);//already added to cart
        }
        
        //produit n'existe pas ==> ajouter le produit dans la commande
        //récuperer le produit
        $produit = $doct->getRepository(Produit::class)->find($id);

        $commandeProduit = new CommandsProduit();

        $commandeProduit->setProduit($produit);
        $commandeProduit->setCommande($commande);
        $commandeProduit->setQuantiteC(1);
        
        $em = $doct->getManager();
        $em->persist($commandeProduit);
        $em->flush();

        return $this->redirectToRoute('app_products', ['added' => 1]);

    }

    
    #[Route('/commands/address', name: 'commands_address')]
    public function commandsAddress(CommandsProduitRepository $commandsProduitRepository,CommandsRepository $commandsRepository,AchatsRepository $achatsRepository , ManagerRegistry $doct): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);
        if($commande == null){
            $commandeAchats = null ;    
        }
        else{
            $commandeAchats = $achatsRepository->findOneBy(["commande" => $commande]);
        }


        if($commandeAchats != null){
            return $this->redirectToRoute('app_commands', ['checkout' => 1]);
        }else{
            if($commande == null){
                return $this->redirectToRoute('app_commands');
            }
            $totalCommandes = $commandsProduitRepository->getCommandesNumber($commande->getId());
            if($totalCommandes == 0){
                return $this->redirectToRoute('app_commands');    
            }
            return $this->redirectToRoute('app_commands', ['address' => 1]);
        }
        
        
    }

    #[Route('/commands/checkout', name: 'commands_checkout')]
    public function commandsCheckout(): Response
    {
        return $this->redirectToRoute('app_commands', ['checkout' => 1]);
    }


    #[Route('/commands/delete-achats/{id}', name: 'deleteAchats')]
    public function deleteAchats(ManagerRegistry $repo, $id): Response
    {

        $achat = $repo->getRepository(Achats::class)->find($id);
        $em = $repo->getManager();
        $em->remove($achat);
        $em->flush();

        return $this->redirectToRoute('app_commands');
    }


    #[Route('/commands/address-update/{id}', name: 'editAddress')]
    public function addressUpdate($id, CommandsRepository $commandsRepository, AchatsRepository $achatsRepository, ManagerRegistry $doct, CommandsProduitRepository $commandsProduitRepository, Request $request): Response
    {
        //************************************************* */
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);
        $commandeAchats = $achatsRepository->findOneBy(["commande" => $commande]);
        if($commande != null)
        {
            $totalCommandes = $commandsProduitRepository->getCommandesNumber($commande->getId());
        }else{
            $totalCommandes = 0;
        }
        //************************************************** */

        $achat = $doct->getRepository(Achats::class)->find($id);

        $form = $this->createForm(AchatsType::class, $achat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $doct->getManager();
            $em->flush();
            return $this->redirectToRoute('app_commands', ['checkout' => 1]);
        }
        return $this->renderForm('front/front-user-commands-Update-Address.html.twig', [
            'title' => 'Zero Waste',
            'commande' => $commande,
            'user' => $user,
            'totalCommandes' => $totalCommandes,
            "formAchats" => $form,
            "commandeAchats" => $commandeAchats,

        ]);
    }


    #[Route('/commands/payment/{test}', name: 'app_commands-paymentSet')]
    public function paymentSet( ManagerRegistry $doct, $test, CommandsRepository $commandsRepository, AchatsRepository $achatsRepository, Request $request): Response
    {

        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);
        $commandeAchats = $achatsRepository->findOneBy(["commande" => $commande]);
        
        if($test == 1){
            $commandeAchats->setPaymentMethod('Stripe');
        }else if($test == 2){
            $commandeAchats->setPaymentMethod('Livraison');
        }else if($test == 3){
            $commandeAchats->setPaymentMethod('Points');
        }
        $em = $doct->getManager();
        $em->flush();

        return $this->redirectToRoute('commands_checkout');
    }

    #[Route('/commands/validate/', name: 'app_commands-validateCheckout')]
    public function validateCheckout( ManagerRegistry $doct, CommandsRepository $commandsRepository, AchatsRepository $achatsRepository, Request $request): Response
    {

        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);
        $commandeAchats = $achatsRepository->findOneBy(["commande" => $commande]);
        
        $commandeAchats->setValidate(1);
        $commande->setStatus(1);
        $em = $doct->getManager();
        $em->flush();

        return $this->redirectToRoute('app_commands');
    }


    #[Route('/dash/admin/commands', name: 'app_dash_admin_commands')]
    public function dashAdminCommands(AchatsRepository $achatsRepository): Response
    {
        $userFullname = "Braiek Ali";

        $achats = $achatsRepository->findBy(['validate' => 1], ['id' => 'DESC']);

        return $this->render('dash_admin/dash-admin-commands.html.twig', [
            'title' => 'Zero Waste',
            'userFullname' => $userFullname,
            'achats' => $achats,
        ]);
    }


}
