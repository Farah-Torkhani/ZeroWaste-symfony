<?php

namespace App\Controller;

use App\Entity\Collecte;
use App\Form\CollecteType;
use App\Repository\CollecteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\CollecteCategorie;
use App\Form\CollecteCategorieType;
use App\Repository\CollecteCategorieRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


use App\Entity\Participation;
use App\Form\ParticipationType;
use App\Repository\ParticipationRepository;


#[Route('/collecte')]
class CollecteController extends AbstractController
{
    #[Route('/', name: 'app_collecte_index', methods: ['GET'])]
    public function index(CollecteRepository $collecteRepository): Response
    {
        return $this->render('collecte/index.html.twig', [
            'collectes' => $collecteRepository->findAll(),
        ]);
    }

    #[Route('/dash_admin_collect', name: 'admine_collecte', methods: ['GET', 'POST'])]
    public function dash_admin_collect(Request $request, CollecteCategorieRepository $collecteCategorieRepository): Response
    {
        return $this->render('collecte/dash_admin_collecte.html.twig', [
            
        ]);
         
    }

    #[Route('/new', name: 'app_collecte_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CollecteRepository $collecteRepository, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $collecte = new Collecte();
        $form = $this->createForm(CollecteType::class, $collecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $em = $doctrine->getManager();
    
                //******************************* */
                $image = $form->get('imageCollect')->getData();
    
                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
    
                    // Move the file to the directory where brochures are stored
                    try {
                        $image->move(
                            $this->getParameter('product_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
    
                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $collecte->setImageCollect($newFilename);
                }
                //***************************** */
                $em->persist($collecte);
                $em->flush();

            return $this->redirectToRoute('app_collecte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('collecte/add_collect.html.twig', [
            'collecte' => $collecte,
            'form' => $form,
            'collectes' => $collecteRepository->findAll(),
        ]);
    }

    #[Route('/new/collecte', name: 'app_collecte', methods: ['GET', 'POST'])]
    public function newCollecte(Request $request, CollecteRepository $collecteRepository): Response
    {
        $collecte = new Collecte();
        $form = $this->createForm(CollecteType::class, $collecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collecteRepository->save($collecte, true);

            return $this->redirectToRoute('app_collecte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('collecte/new.html.twig', [
            'collecte' => $collecte,
            'form' => $form,
            'collectes' => $collecteRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_collecte_show', methods: ['GET'])]
    public function show(Collecte $collecte): Response
    {
        return $this->render('collecte/show.html.twig', [
            'collecte' => $collecte,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_collecte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Collecte $collecte, CollecteRepository $collecteRepository): Response
    {
        $form = $this->createForm(CollecteType::class, $collecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collecteRepository->save($collecte, true);

            return $this->redirectToRoute('app_collecte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('collecte/edit.html.twig', [
            'collecte' => $collecte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_collecte_delete', methods: ['POST'])]
    public function delete(Request $request, Collecte $collecte, CollecteRepository $collecteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collecte->getId(), $request->request->get('_token'))) {
            $collecteRepository->remove($collecte, true);
        }

        return $this->redirectToRoute('app_collecte_index', [], Response::HTTP_SEE_OTHER);
    }

/*********************************************************************************************** */
/************************************************************************************************************ */  
/************************************************************************************************************ */  

    #[Route('/categorie/cat', name: 'app_collecte_categorie_index', methods: ['GET'])]
    public function indexCat(CollecteCategorieRepository $collecteCategorieRepository): Response
    {
        return $this->render('collecte_categorie/index.html.twig', [
            'collecte_categories' => $collecteCategorieRepository->findAll(),
        ]);
    }

    #[Route('/categorie/newCat', name: 'app_collecte_categorie_new', methods: ['GET', 'POST'])]
    public function newCat(Request $request, CollecteCategorieRepository $collecteCategorieRepository): Response
    {
        $collecteCategorie = new CollecteCategorie();
        $form = $this->createForm(CollecteCategorieType::class, $collecteCategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $collecteCategorieRepository->save($collecteCategorie, true);

            return $this->redirectToRoute('app_collecte_categorie_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('collecte_categorie/add_catcollecte.html.twig', [
            'collecte_categorie' => $collecteCategorie,
            'form' => $form,
            'collecte_categories' => $collecteCategorieRepository->findAll(),
        ]);
         
    }

    #[Route('/categorie/dash_admin_collectCat', name: 'admin_collecte', methods: ['GET', 'POST'])]
    public function dash_admin_collectCat(Request $request, CollecteCategorieRepository $collecteCategorieRepository): Response
    {
        return $this->render('collecte_categorie/dash_admin_collecte.html.twig', [
            
        ]);
         
    }

    #[Route('/categorie/{id}', name: 'app_collecte_categorie_delete', methods: ['GET'])]
    public function deleteCat(Request $request, CollecteCategorie $collecteCategorie, CollecteCategorieRepository $collecteCategorieRepository): Response
    {
        
            $collecteCategorieRepository->remove($collecteCategorie, true);
        

        return $this->redirectToRoute('app_collecte_categorie_new', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/categorie/{id}/editCat', name: 'app_collecte_categorie_edit', methods: ['GET', 'POST'])]
    public function editCat(Request $request, CollecteCategorie $collecteCategorie, CollecteCategorieRepository $collecteCategorieRepository): Response
    {
        $form = $this->createForm(CollecteCategorieType::class, $collecteCategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collecteCategorieRepository->save($collecteCategorie, true);

            return $this->redirectToRoute('app_collecte_categorie_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('collecte_categorie/category_update.html.twig', [
            'collecte_categorie' => $collecteCategorie,
            'form' => $form,
        ]);
    }
  /************************************************************************************************************ */  
  /************************************************************************************************************ */  
  /************************************************************************************************************ */  
    #[Route('/participation/new/{id}', name: 'app_participation_new', methods: ['GET', 'POST'])]
    public function newPart(Request $request, ParticipationRepository $participationRepository,CollecteRepository $collecteRepository,int $id): Response
    {   $collecte = $collecteRepository->find($id);
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $participation->addCollecteId($collecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participationRepository->save($participation, true);

            return $this->redirectToRoute('app_participation_new', ['id' => $collecte->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/add_participation.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/participation/affiche', name: 'app_participation_index', methods: ['GET'])]
    public function indexpart(ParticipationRepository $participationRepository): Response
    {  
         $participations = $this->getDoctrine()->getRepository(Participation::class)->findAll();
        return $this->render('participation/dash_admin_participation.html.twig', [
            'participations' => $participations,
            'collect' => $participations,
        ]);
    }

    #[Route('/participation/affiche/{id}', name: 'app_participation_delete',methods: ['GET'])]
    public function deletepart(Request $request, Participation $participation, ParticipationRepository $participationRepository): Response
    {
       
            $participationRepository->remove($participation, true);
        

        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
    }

    

    /************************************************************************************************************ */  
    /************************************************************************************************************ */  
    /************************************************************************************************************ */  
    




 

}


