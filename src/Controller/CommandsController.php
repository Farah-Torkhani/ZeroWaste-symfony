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
use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AchatsType;
use App\Repository\AchatsRepository;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Endroid\QrCode\QrCode;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Psr\Log\LoggerInterface;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Label\Font\NotoSans;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use TCPDF;



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
            'stripe_key' => $_ENV["STRIPE_PUBLIC_KEY"],
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
        $userFullname = "Asma Bouraoui";

        $achats = $achatsRepository->findBy(['validate' => 1], ['id' => 'DESC']);

        return $this->render('dash_admin/dash-admin-commands.html.twig', [
            'title' => 'Zero Waste',
            'userFullname' => $userFullname,
            'achats' => $achats,
        ]);
    }


    #[Route('/pdf', name: 'pdf')]
    public function pdf(AchatsRepository $Repository)
    { 
        $pdf = new TCPDF();
        $pdf->AddPage();
      
        $imagePath = $this->getParameter('kernel.project_dir') . './contents/img/ali.jpg';
        $pdf->Image($imagePath, 5, 5, 25, 25, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
                // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Open Sans');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('front/pdf.html.twig', [
            'achats' => $Repository->findAll(),
        ]);

        // Add header HTML to $html variable
        $headerHtml = '<h1 style="text-align: center; color: #32bb6f;">Liste des clients</h1>';
        $html = $headerHtml . $html;

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

         // Get the output buffer and clear it
    $pdfOutput = $dompdf->output();
    ob_clean();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("ListeDesClients.pdf", [
            'achats' => true
        ]);

         // Send the PDF to the browser
    $response = new Response($pdfOutput, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="ListeDesClients.pdf"',
    ]);
    }
//qr
    #[Route('/qr-codes', name: 'app_qr_codes')]
    public function index(): Response
    {
        $writer = new PngWriter();
        $qrCode = QrCode::create("SpecialOffer10 for 10% discount")
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(120)
            ->setMargin(0)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        $logo = Logo::create('./contents/img/logo.png')
            ->setResizeToWidth(60);
        $label = Label::create('')->setFont(new NotoSans(8));
 
        $qrCodes = [];
        $qrCodes['img'] = $writer->write($qrCode, $logo)->getDataUri();
        $qrCodes['simple'] = $writer->write(
                                $qrCode,
                                null,
                                $label->setText('discount code')
                            )->getDataUri();
 
 
        return $this->render('qr_code_generator/qr.html.twig', $qrCodes);
    }

    //promo
  
    #[Route('/commands/promo', name: 'app_commands-promo')]
   

    //Best seller
    #[Route('/most', name: 'most')]
 
public function mostCommandedProduct(CommandsProduitRepository $repository, Request $request)
{
    $mostCommandedProduct = $repository->findMostCommandedProduct();

    if ($request->isXmlHttpRequest()) {
        return $this->render('front/most_commanded_product.html.twig', [
            'mostCommandedProduct' => $mostCommandedProduct,
        ]);
    }

    return $this->render('front/bestseller.html.twig', [
        'mostCommandedProduct' => $mostCommandedProduct,
    ]);
}
    
   
//************************************************************************** */
    //JSOOOOOOOOOOOOOOOOOOOOOOOOOOOOON
/************************************************************************ */

//json
#[Route("/afficheJson", name:"app_afficheJs")]
public function getCommandsss(CommandsRepository $repo, SerializerInterface $serializer)
{
    $commande = $repo->findAll();
    //* Nous utilisons la fonction normalize qui transforme le tableau d'objets 
    //* students en  tableau associatif simple.
    // $studentsNormalises = $normalizer->normalize($students, 'json', ['groups' => "students"]);

    // //* Nous utilisons la fonction json_encode pour transformer un tableau associatif en format JSON
    // $json = json_encode($studentsNormalises);

    $json = $serializer->serialize($commande, 'json', ['groups' => "commands"]);

    //* Nous renvoyons une réponse Http qui prend en paramètre un tableau en format JSON
    return new Response($json);
}
#[Route("/JasonAfficher", name:"affiche")]
    //* Dans cette fonction, nous utilisons les services NormlizeInterface et CommandsRepository, 
    //* avec la méthode d'injection de dépendances.
    public function getCommands(CommandsRepository $repo, SerializerInterface $serializer)
    {
        $commande = $repo->findAll();
        //* Nous utilisons la fonction normalize qui transforme le tableau d'objets 
        //* students en  tableau associatif simple.
        // $studentsNormalises = $normalizer->normalize($students, 'json', ['groups' => "students"]);

        // //* Nous utilisons la fonction json_encode pour transformer un tableau associatif en format JSON
        // $json = json_encode($studentsNormalises);

        $json = $serializer->serialize($commande, 'json', ['groups' => "commands"]);

        //* Nous renvoyons une réponse Http qui prend en paramètre un tableau en format JSON
        return new Response($json);
    }

//recuperer commande par id jason but i dont mainly have it :D
#[Route("/Commands/{id}", name: "commands")]
public function CommandsId($id, NormalizerInterface $normalizer, CommandsRepository $repo)
{
    $commands = $repo->find($id);
    $commandsNormalises = $normalizer->normalize($commands, 'json', ['groups' => "students"]);
    return new Response(json_encode($commandsNormalises));
}   

//jason ajout commande 
#[Route('/commandesJason/add/{id}', name: 'app_commandes-addJason')]
public function addcommandeJason(Request $req, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $commande = new Commands();
        $commande->setStatus(0);
      

        $em->persist($commande);
        $em->flush();

        $jsonContent = $Normalizer->normalize($commande, 'json', ['groups' => 'commands']);
        return new Response(json_encode($jsonContent));
    }

//delete jason

#[Route('/deletecomJson/{id}', name:'deleteJson')]
public function commandsDeleteJason(Request $req, $id, NormalizerInterface $Normalizer)
{
    $em = $this->getDoctrine()->getManager();
    $commande = $em->getRepository(Commands::class)->find($id);
    $em->remove($commande);
    $em->flush();
    $jsonContent = $Normalizer ->normalize($commande, 'json', ['groups' => 'commands']);
    return new Response("Commande deleted successfully" . json_encode($jsonContent));
}

}