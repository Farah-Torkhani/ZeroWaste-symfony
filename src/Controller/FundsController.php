<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\FundrisingRepository;
use App\Entity\Fundrising;
use App\Entity\User;
use App\Form\FundrisingType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;


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

    #[Route('/afficherFundsdetail/{id}', name: 'funds_detail')]
    public function afficherfundsdetail(FundrisingRepository $Fundrising, ManagerRegistry $doct,$id): Response
    {

        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $stu = $doct->getRepository(Fundrising::class)->findOneBy(['id' => $id]);

               return $this->render('funds/ListFund-details.html.twig', array('stu' => $stu,"user"=>$user));

    }

    #[Route('/afficherFunds', name: 'add_funds')]
    public function afficher(FundrisingRepository $Fundrising, ManagerRegistry $doct): Response
    {

        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $stu = $Fundrising->findAll();

               return $this->render('funds/listfund.html.twig', array('Funds' => $stu,"user"=>$user));

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

]);    }
    

    #[Route('/afficherFundrising_dashA', name: 'app_afficherFundrising_dashA')]
    
    public function afficherFundrisingAdmin(FundrisingRepository $Fundrising, ManagerRegistry $doct): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $stu = $Fundrising->findAll();

        return $this->render('funds/admin-Don.html.twig', array('Funds' => $stu,"user"=>$user));
    }

    #[Route('/afficherFundrising_dashAssoc', name: 'app_afficherFundrising_dashAssoc')]
    
    public function afficherFundrisingAssoc(FundrisingRepository $Fundrising, ManagerRegistry $doct): Response
    {
         $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $stu = $Fundrising->findAll();

        return $this->render('funds/association-Don.html.twig', array('Funds' => $stu,"user"=>$user));
    }

    #[Route('deleteFunds/{id}', name: 'app_deleteFunds')]
    public function deleteFunds(FundrisingRepository $Fundrising, ManagerRegistry $man, $id): Response
    {
        $stu = $Fundrising->find($id);
        $EntityManager = $man->getManager();
        $EntityManager->remove($stu);
        $EntityManager->flush();

        return $this->redirectToRoute('app_afficherFundrising_dashAssoc');
    }


 
    #[Route('/find', name: 'app_fundfind')]
    public function product(FundrisingRepository $Fundrising,ManagerRegistry $man): Response
    {
        $Fundrising = $Fundrising->findAll();
        $funds = $man->getRepository(Fundrising::class)->findAll();

        return $this->render('funds/association-Don.html.twig', array("Funds"=>$Fundrising,"fundrising1"=>$funds));
    }

    #[Route('/updatep/{id}', name:'updateFunds')]
    public function updatep(Request $request, ManagerRegistry $doctrine, $id):Response{
    
    $Fundrising = $doctrine->getRepository(Fundrising::class)->find($id);

    $form = $this->createForm(FundrisingType::class, $Fundrising);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $Fundrising = $form->getData();
        $entityManager = $doctrine->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('app_fundfind');
    }
    return $this->renderForm('funds/association-Don-Add.html.twig', [
        'form' => $form
    ]);

    }

  
 

}
