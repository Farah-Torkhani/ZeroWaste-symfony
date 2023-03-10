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

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use App\Entity\Participation;
use App\Form\ParticipationType;
use App\Repository\ParticipationRepository;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

use DateTime;





class CollecteController extends AbstractController
{   
    #[Route('/collecte', name: 'app_collecte_index', methods: ['GET'])]
    public function index(CollecteRepository $collecteRepository, CollecteCategorieType $catRepo, Request $request, CacheInterface $cache){
        // On définit le nombre d'éléments par page
        $limit = 10;

        // On récupère le numéro de page
        $page = (int)$request->query->get("page", 1);

        // On récupère les filtres
        $filters = $request->get("categories");

        // On récupère les annonces de la page en fonction du filtre
        $annonces = $collecteRepository->getPaginatedAnnonces($page, $limit, $filters);

        // On récupère le nombre total d'annonces
        $total = $collecteRepository->getTotalAnnonces($filters);

        // On vérifie si on a une requête Ajax
        if($request->get('ajax')){
            return new JsonResponse([
                'content' => $this->renderView('collecte/collecte.html.twig', compact('annonces', 'total', 'limit', 'page'))
            ]);
        }

        // On va chercher toutes les catégories
        $categories = $cache->get('categories_list', function(ItemInterface $item) use($catRepo){
            $item->expiresAfter(3600);

            return $catRepo->findAll();
        });

        return $this->render('collecte/collecte.html.twig', compact('annonces', 'total', 'limit', 'page', 'categories'));
    }
   
    #[Route('/collecte', name: 'app_collecte_index', methods: ['GET'])]
    public function app_collecte_index(CollecteRepository $collecteRepository,CollecteCategorieRepository $collecteCategorieRepository): Response
    {   
        $collectes = $collecteRepository->findAll();
        $collectes = $collecteRepository->findAllOrderByDateDeb();
        return $this->render('collecte/collecte.html.twig', [
            'collectes' => $collectes , 
            'categorie' => $collecteCategorieRepository->findAll(),
        ]);
    }

    #[Route('/collecte/add/filterCategories', name: 'filterCategories')]
    public function filterCategories(CollecteRepository $collecteRepository, Request $request, NormalizerInterface $Normalizer): Response
    {
        $id =$request->query->get('categoryIdData');
        //$id = "37";
       // var_dump($id);// kan theb tasti chouf l console , kan temchi lel lien mtaa func hedhi taw tel9aha dima null
        $products = $collecteRepository->filterCategories($id);
        //$products = $collecteRepository->findAll();
        
        
        $json = $Normalizer->normalize($products, 'json', ['groups' => 'produit_group']);

        $jsonString = json_encode($json);
        //var_dump($jsonString); // hedhi taamel erreur qui tji tasti biha 3leh allahou a3lam 😅
        return new Response($jsonString);
    }

/*****************************calender********************************************************** */    
    #[Route('/collecte/dash_admin_collect', name: 'admine_collecte', methods: ['GET', 'POST'])]
    public function dash_admin_collect(Request $request, CollecteCategorieRepository $collecteCategorieRepository,CollecteRepository $Collecte): Response
    {
       
        $events=$Collecte->findAll();

        $rdvs = [];

        foreach($events as $event){
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getDateDeb()->format('Y-m-d H:i:s'),
                'end' => $event->getDateFin()->format('Y-m-d H:i:s'),
                'title' => $event->getNomCollecte(),
                'description' => $event->getDescription(),
                'etat' => $event->getEtat(),
                'adresse' => $event->getAdresse(),
                'image' => $event->getImageCollect(),
                'backgroundColor'=> "rgba(102,108,255, 0.12)",
                'borderColor'=> "rgba(102,108,255, 0.12)",
                'textColor'=> "rgba(102,108,255)",
                'url' => "http://127.0.0.1:8000/collecte/new",
                
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('collecte/dash_admin_collecte.html.twig', compact('data'));
         
    }

    

    #[Route('/collecte/api/edit', name: 'api_event_edit')]
    public function majEvent( Request $request,ManagerRegistry $doct): Response
    {
        $collecteT = $request->get('donneesT');
        $collecteS = $request->get('donneesS');
        $collecteE = $request->get('donneesE');
        //$donnees = json_decode($request->getContent());
        //$id = $doct->getRepository(Collecte::class)->find($collecteT);
        $id = $doct->getRepository(Collecte::class)->findOneBy(["nomCollecte" => $collecteT]);


            // On hydrate l'objet avec les données
           
            $id->setDateDeb(new DateTime($collecteS));
            $id->setNomCollecte("gul2");
            

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // On retourne le code
            return new Response('Ok');
    

       
    }


/*****************************calender********************************************************** */  
    #[Route('/collecte/new', name: 'app_collecte_new', methods: ['GET', 'POST'])]
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

    #[Route('/collecte/new/collecte', name: 'app_collecte', methods: ['GET', 'POST'])]
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

    #[Route('/collecte/show/{id}', name: 'app_collecte_show', methods: ['GET'])]
    public function show(Collecte $collecte): Response
    {
        return $this->render('collecte/show.html.twig', [
            'collecte' => $collecte,
        ]);
    }

    #[Route('/collecte/{id}/edit', name: 'app_collecte_edit', methods: ['GET', 'POST'])]
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

    #[Route('/collecte/delete/{id}', name: 'app_collecte_delete', methods: ['POST'])]
    public function delete(Request $request, Collecte $collecte, CollecteRepository $collecteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collecte->getId(), $request->request->get('_token'))) {
            $collecteRepository->remove($collecte, true);
        }

        return $this->redirectToRoute('admine_collecte', [], Response::HTTP_SEE_OTHER);
    }

/*********************************************************************************************** */
/************************************************************************************************************ */  
/************************************************************************************************************ */  

    #[Route('/collecte/categorie/cat', name: 'app_collecte_categorie_index', methods: ['GET'])]
    public function indexCat(CollecteCategorieRepository $collecteCategorieRepository): Response
    {
        return $this->render('collecte_categorie/index.html.twig', [
            'collecte_categories' => $collecteCategorieRepository->findAll(),
        ]);
    }

    #[Route('/collecte/categorie/newCat', name: 'app_collecte_categorie_new', methods: ['GET', 'POST'])]
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

    #[Route('/collecte/categorie/dash_admin_collectCat', name: 'admin_collecte', methods: ['GET', 'POST'])]
    public function dash_admin_collectCat(Request $request, CollecteCategorieRepository $collecteCategorieRepository): Response
    {
        return $this->render('collecte_categorie/dash_admin_collecte.html.twig', [
            
        ]);
         
    }

    #[Route('/collecte/categorie/{id}', name: 'app_collecte_categorie_delete', methods: ['GET'])]
    public function deleteCat(Request $request, CollecteCategorie $collecteCategorie, CollecteCategorieRepository $collecteCategorieRepository): Response
    {
        
            $collecteCategorieRepository->remove($collecteCategorie, true);
        

        return $this->redirectToRoute('app_collecte_categorie_new', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/collecte/categorie/{id}/editCat', name: 'app_collecte_categorie_edit', methods: ['GET', 'POST'])]
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
    #[Route('/collecte/participation/new/{id}', name: 'app_participation_new', methods: ['GET', 'POST'])]
    public function newPart(Request $request, ParticipationRepository $participationRepository,CollecteRepository $collecteRepository, ManagerRegistry $doctrine, SluggerInterface $slugger,int $id): Response
    {   $collecte = $collecteRepository->find($id);
        $participation = new Participation();
        $participation->addCollecteId($collecte);
        $form = $this->createForm(ParticipationType::class, $participation); 
        
        $participation->setVerificationP(0);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
         
            //******************************* */
            $image = $form->get('image')->getData();

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
                $participation->setImage($newFilename);
            }
            //***************************** */
            $em->persist($participation);
            $em->flush();

            return $this->redirectToRoute('app_participation_new', ['id' => $collecte->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('participation/add_participation.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/collecte/participation/affiche', name: 'app_participation_index', methods: ['GET'])]
    public function indexpart(ParticipationRepository $participationRepository): Response
    {  
         $participations = $this->getDoctrine()->getRepository(Participation::class)->findAll();
        return $this->render('participation/dash_admin_participation.html.twig', [
            'participations' => $participations,
            'collect' => $participations,
        ]);
    }

    #[Route('/collecte/participation/affiche/{id}', name: 'app_participation_delete',methods: ['GET'])]
    public function deletepart(Request $request, Participation $participation, ParticipationRepository $participationRepository): Response
    {
       
            $participationRepository->remove($participation, true);
        

        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
    }

    

    /************************************************************************************************************ */  
    /************************************************************************************************************ */  
    /************************************************************************************************************ */  
    
    #[Route('/collecte/sendmail/{id}', name: 'app_sendmail')]
    public function app_sendmail(ParticipationRepository $ParticipationRepository,Request $request,MailerInterface $mailer,int $id): Response
    {   
        //$form = $this->createForm(SendmailType::class);

        //$form->handleRequest($request);
        $form  = $ParticipationRepository->find($id);
        $participations = $this->getDoctrine()->getRepository(Participation::class)->findAll();
            //$data = $form->getData();
            
            //$adress = $data['email'];
            //$content = $data['content'];
            
            $email = (new Email())
            ->from('kun.elghoul@gmail.com')
            ->to($form->getEmail())
            ->subject('sujet de mail')
            ->text('acceptation');
            

        $mailer->send($email);
            

        
        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
    }
/**************************************mobile******************************************************** */

    #[Route("/Allcollects", name: "list")]
    public function getJollect(CollecteRepository $repo, SerializerInterface $serializer)
    {
        $students = $repo->findAll();
        $json = $serializer->serialize($students, 'json', ['groups' => "produit_group"]);
        return new Response($json);
    }
    

    #[Route("addCollecteJSON/new", name: "addCollecteJSON")]
    public function addCollecteJSON(Request $req,   NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $collecte = new Collecte();

        $dateString=$req->get('date_deb');
        $collecte1=\DateTime::createFromFormat('Y-m-d H:i:s', $dateString);
        
        if ($collecte1 === false) {
            throw new \Exception('Failed to create DateTime from string: '.$dateString);
        }

        $dateString2=$req->get('date_fin');;
        $collecte2=\DateTime::createFromFormat('Y-m-d H:i:s', $dateString2);

        
        
        if ($collecte2 === false) {
            throw new \Exception('Failed to create DateTime from string: '.$dateString2);
        }

        $collecte->setNomCollecte($req->get('nomCollecte'));
        $collecte->setDescription($req->get('description'));
        $collecte->setImageCollect($req->get('imageCollect'));
        $collecte->setEtat(0);
        $collecte->setAdresse($req->get('adresse'));
        $collecte->setDateDeb($collecte1);
        $collecte->setDateFin($collecte2);
       


        $em->persist($collecte);
        $em->flush();

        $jsonContent = $Normalizer->normalize($collecte, 'json', ['groups' => 'produit_group']);
        return new Response(json_encode($jsonContent));
    }


    #[Route("/collecte/updateCollecteJSON/{id}", name: "updateCollecteJSON")]
    public function updateCollecteJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {
       
        $em = $this->getDoctrine()->getManager();
        $Collecte = $em->getRepository(Collecte::class)->find($id);
        
        
        $dateString=$req->get('date_deb');
        $collecte1=\DateTime::createFromFormat('Y-m-d H:i:s', $dateString);
        
        if ($collecte1 === false) {
            throw new \Exception('Failed to create DateTime from string: '.$dateString);
        }

        $dateString2=$req->get('date_fin');;
        $collecte2=\DateTime::createFromFormat('Y-m-d H:i:s', $dateString2);
        
        if ($collecte2 === false) {
            throw new \Exception('Failed to create DateTime from string: '.$dateString2);
        }

        $Collecte->setNomCollecte($req->get('nomCollecte'));
        $Collecte->setDescription($req->get('description'));
        $Collecte->setImageCollect($req->get('imageCollect'));
        $Collecte->setEtat(0);
        $Collecte->setAdresse($req->get('adresse'));
        $Collecte->setDateDeb($collecte1);
        $Collecte->setDateFin($collecte2);
       

        $em->flush();

        $jsonContent = $Normalizer->normalize($Collecte, 'json', ['groups' => 'produit_group']);
        return new Response("Collecte updated successfully " . json_encode($jsonContent));
    }



    #[Route("/collecte/deleteStudentJSON/{id}", name: "deleteStudentJSON")]
    public function deleteStudentJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository(Collecte::class)->find($id);
        $em->remove($student);
        $em->flush();
        $jsonContent = $Normalizer->normalize($student, 'json', ['groups' => 'produit_group']);
        return new Response("Student deleted successfully " . json_encode($jsonContent));
    }


    #[Route("/collecte/show/show/{id}", name: "collecteShow")]
    public function collecteShow($id, NormalizerInterface $normalizer, CollecteRepository $repo)
    {
        $collecte = $repo->find($id);
        $collecteNormalises = $normalizer->normalize($collecte, 'json', ['groups' => "produit_group"]);
        return new Response(json_encode($collecteNormalises));
    }
   

 /************************************************************************************************** */

}


