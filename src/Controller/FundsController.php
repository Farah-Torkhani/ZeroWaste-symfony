<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\FundrisingRepository;
use App\Entity\Fundrising;
use App\Entity\User;
use App\Entity\DonHistory;
use App\Form\FundrisingType;
use App\Form\SearchFundType;
use App\Repository\DonHistoryRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;




class FundsController extends AbstractController
{
    
    #[Route('/funds', name: 'app_funds')]
    public function index(): Response
    {
        return $this->render('funds/admin-Don.html.twig', [
            'controller_name' => 'FundsController',
        ]);
    }

    #[Route('/addfunds', name: 'add_funds')]
    public function addfun(): Response
    {
        return $this->render('funds/association-Don-Add.html.twig', [
            'controller_name' => 'FundsController',
        ]);
    }

    #[Route('/afficherFundsdetail/{id}', name: 'afficherFundsdetail')]
    public function afficherfundsdetail(Request $request, Fundrising $fund, UserRepository $userRepo): Response
    {

        $user = $userRepo->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        

        return $this->render('funds/ListFund-details.html.twig', array('stu' => $fund,"user"=>$user,"donHistory"=>$fund->getDonHistories()));

    }

    #[Route('/afficherFunds', name: 'afficher_funds')]
    public function afficher(FundrisingRepository $Fundrising, ManagerRegistry $doct): Response
    {

        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $stu = $Fundrising->findAll();

               return $this->render('funds/listfund.html.twig', array('Funds' => $stu,"user"=>$user));

    }

    // #[Route('/afficherFundsMobile', name: 'afficher_funds_mobile')]
    // public function afficherMobile(FundrisingRepository $Fundrising, ManagerRegistry $doct): Response
    // {

    //     $stu = $Fundrising->findAll();
    //     foreach($stu as $item) {
    //         $arrayCollection[] = array(
    //             'id' => $item->getId(),
    //             'titreDon' => $item->getTitreDon(),
    //             'description' => $item->getDescriptionDon(),
    //             'ImageDon' => $item->getImageDon(),
    //             'Date don' => $item->getDateDon(),
    //             'Date don limite' => $item->getDateDonLimite(),
    //             'etat' => $item->getEtat(),
    //             'objectif' => $item->getObjectif(),
                
    //         );
    //    }
    //            return $this->json([
    //             $arrayCollection
    //         ]);
                 
    // }
    #[Route('afficherFundsMobilee', name:'listfunds')]
    public function getFunds(FundrisingRepository $Fundrising, NormalizerInterface $normalizer)
    {
        $stu = $Fundrising->findAll();
        $jsonContent = $normalizer->normalize($stu, 'json', ['groups' => "funds"]);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/addFunds1', name: 'app_addFundrising')]
    public function addfunds(\Doctrine\Persistence\ManagerRegistry $doctrine, Request $request , SluggerInterface $slugger)
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $Fundrising = new Fundrising();
        $form = $this->createForm(FundrisingType::class, $Fundrising);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() ) {
            $em = $doctrine->getManager();
            $Fundrising->setDateDon(new \DateTime());
           
            $photo = $form['imageDon']->getData();
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter(name :'fund_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'photo$photoname' property to store the PDF file name
                // instead of its contents
                $Fundrising->setImageDon($newFilename);
            }

            $em->persist($Fundrising);
            $em->flush();
            

            return $this->redirectToRoute('app_afficherFundrising_dashAssoc');
        }
return $this->renderForm("funds/association-Don-Add.html.twig", [
    'form' => $form,
    'user'=>$user

]);    }
    






    #[Route('/addFunds1_mobile', name: 'app_addFundrising_mobile')]
    public function ajoutfunds(Request $req,NormalizerInterface $Normalizer,\Doctrine\Persistence\ManagerRegistry $doctrine,)
    {
       $em = $this->getDoctrine()->getManager();
       $Fundrising = new Fundrising();

       $Fundrising->setTitreDon($req->get('TitreDon'));
       $Fundrising->setDescriptionDon($req->get('descriptionDon'));
       $Fundrising->setEtat("");
       $Fundrising->setObjectif(0);
       $em->persist($Fundrising);
       $em->flush();
       $FundrisingNormalises = $Normalizer->normalize($Fundrising,'json',['groups' => "funds"]);
       $json = json_encode($FundrisingNormalises);
       return new Response($json);
    }
     #[Route('deleteFundsMobilee/{id}' , name: 'deletefundsMobile')]
    public function deleteFundsMobile(FundrisingRepository $Fundrising, ManagerRegistry $man,ManagerRegistry $doct,$id,NormalizerInterface $normalizer ): Response
    {
       
        $stu = $Fundrising->find($id);
        $EntityManager = $man->getManager();
        $funds = $Fundrising->find($id) ;
        $EntityManager->remove($stu);
        $EntityManager->flush();
        $donNormalizes = $normalizer->normalize($funds, 'json', ["groups"=>"funds"]);
       $json = json_encode($donNormalizes) ;
        return new Response( "don deleted successfully" . $json) ;
    }


    #[Route('/RgetbyidMOBILE/{id}', name: 'funds_parid', methods:'GET')]
    
    public function fundsparid(  FundrisingRepository $repo , NormalizerInterface $normalizer , $id): Response
    {
   $funds = $repo->find($id) ;
   $fundsNormalises = $normalizer->normalize($funds,'json' , ['groups'=>"funds:read"]) ;
$json = json_encode($fundsNormalises) ;
      return new Response($json) ;
    }



    #[Route('/afficherFundrising_dashA', name: 'app_afficherFundrising_dashA')]
    
    public function afficherFundrisingAdmin(FundrisingRepository $Fundrising, ManagerRegistry $doct): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $stu = $Fundrising->findAll();

        return $this->render('funds/admin-Don.html.twig', array('Funds' => $stu,"user"=>$user));
    }

    #[Route('/{id}/donators', name: 'app_donators_dashA')]
    
    public function donators(Fundrising $fund, UserRepository $userRepo): Response
    {
        $user = $userRepo->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $donations = [];
        foreach ($fund->getDonHistories() as $donation) {
            array_push($donations, [
                "user" => $donation->getUserID()->getFullName(),
                "amount" =>$donation->getDonationPrice(),
                "comment" =>$donation->getComment()
            ]);
        }

        return $this->render('funds/donators.html.twig', array('donations' => $donations,"user"=>$user));
    }


    #[Route('/TriPAB', name: 'app_tri_dateA')]
    public function TriPBackA(FundrisingRepository $repository, ManagerRegistry $doct)
    {  
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
    
        $plat = $repository->orderByDateASC();
        return $this->render("funds/association-Don.html.twig", array("Funds" => $plat, "user"=>$user));
    }



    #[Route('search', name: 'funds_search')]
    public function search(FundrisingRepository $funds, Request $request, ManagerRegistry $doct): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $issetTitre = isset($_GET['titreDon']);
        if ($issetTitre) {
            
            $Funds = $funds->searchfunds($_GET['titreDon']);
    
            return $this->render('funds/listFund.html.twig', [
                'Funds' => $Funds,
                'user' => $user
            ]);
        }
    
        return $this->render('funds/search.html.twig', [
            'user' => $user,
            'funds' => $funds,
        ]);
    }


    

    #[Route('/admin/traiter/{id}', name: 'smsparticipation')]
    function Traiter(FundrisingRepository $repository, $id, Request $request, ManagerRegistry $doct, UserRepository $repo)
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $user->eraseCredentials();
        $Fundrising = new Fundrising();
        $user = $repo->find($id);
        $em = $doct->getManager();
        $em->flush();
        $repository->sms();
      
        $em->flush();
        $this->addFlash('danger', 'reponse envoyée avec succées');
        return $this->redirectToRoute('afficher_funds');
    }

    #[Route('/afficherFundrising_dashAssoc', name: 'app_afficherFundrising_dashAssoc')]
    
    public function afficherFundrisingAssoc(FundrisingRepository $Fundrising, DonHistoryRepository $donHistoryRepository, ManagerRegistry $doct): Response
    {

        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $stu = $Fundrising->findAll();
        $notCompleted_funds = $Fundrising->findBy(['etat' => 2]);
        $total = 0;
        foreach($stu as $fund) {
            $total = $donHistoryRepository->getTotalDonations($fund->getId());
            $fund->setTotal($total);
        }

        return $this->render('funds/association-Don.html.twig', array('Funds' => $stu,"user"=>$user, "funds_notCompleted" => count($notCompleted_funds)));
    }

    #[Route('deleteFunds/{id}', name: 'app_deleteFunds')]
    public function deleteFunds(FundrisingRepository $Fundrising, ManagerRegistry $man,ManagerRegistry $doct,$id): Response
    {
       
        $stu = $Fundrising->find($id);
        $EntityManager = $man->getManager();
        $EntityManager->remove($stu);
        $EntityManager->flush();

        return $this->redirectToRoute('app_afficherFundrising_dashAssoc');
    }


 


 
    #[Route('/find', name: 'app_fundfind')]
    public function product(FundrisingRepository $Fundrising,ManagerRegistry $man, ManagerRegistry $doctrine): Response
    {
        $notCompleted_funds = $Fundrising->findBy(['etat' => 2]);
        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $Fundrising = $Fundrising->findAll();
        $funds = $man->getRepository(Fundrising::class)->findAll();

        return $this->render('funds/association-Don.html.twig', array("Funds"=>$Fundrising,"fundrising1"=>$funds, 'user'=>$user , "funds_notCompleted" => count($notCompleted_funds)));
       
    }

 

    #[Route('/updatep/{id}', name:'updateFunds')]
    public function updatep(Request $request, ManagerRegistry $doctrine, $id):Response{
        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
    $Fundrising = $doctrine->getRepository(Fundrising::class)->find($id);
    $notCompleted_funds = $Fundrising->findBy(['etat' => 2]);
    $form = $this->createForm(FundrisingType::class, $Fundrising);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $Fundrising = $form->getData();
        $entityManager = $doctrine->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('app_fundfind');
    }
    return $this->renderForm('funds/association-Don-Add.html.twig', [
        'form' => $form ,
        'user' => $user,
        "funds_notCompleted" => count($notCompleted_funds)
   
    ]);

    }

  
    #[Route('/rechercheAjax', name: 'rechercheAjax')]
    public function searchAjax(Request $request, FundrisingRepository $Fundrising, ManagerRegistry $doctrine)
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        // Récupérez le paramètre de recherche depuis la requête
        $query = $request->query->get('q');
        // Récupérez les funds correspondants depuis la base de données
        $Funds = $Fundrising->findDonByName($query);
        $html = $this->renderView("funds/listFund.html.twig", [
            "Funds" => $Funds,'user' => $user,
        ]);
    
        return new Response($html);
    }




}
