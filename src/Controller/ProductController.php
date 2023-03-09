<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Produit;

use App\Repository\CategorieProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\CategorieProduit;
use App\Form\CategorieProduitType;
use App\Form\ProductType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\CommandsRepository;
use App\Repository\CommandsProduitRepository;
use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\OffreType;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

use App\Entity\ProductFavoris;
use App\Repository\ProductFavorisRepository;
use Facebook\Facebook;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use GuzzleHttp\Client;

use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\File;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;

use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\Image;

use App\Form\SearchProductType;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use DateTime;
use App\Entity\UserNotification;
use App\Repository\UserNotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/products', name: 'app_products')]
    public function products(NotificationRepository $notif, ProduitRepository $produitRepository, CommandsRepository $commandsRepository, CommandsProduitRepository $commandsProduitRepository,  CategorieProduitRepository $categorieProduitRepository, Request $request, ManagerRegistry $doctrine,  SluggerInterface $slugger): Response
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
       
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);
        if($commande != null)
        {
            $totalCommandes = $commandsProduitRepository->getCommandesNumber($commande->getId());
        }else{
            $totalCommandes = 0;
        }

        $notifications = $notif->getUserNotifications($user);
        
        $products = $produitRepository->findAll();
        //$categories = $categorieProduitRepository->findAll();
        $categories = $categorieProduitRepository->getAllCategoriesSortedByOrderCateg();

        $form = $this->createForm(SearchProductType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() ) {
            
            //******************************* */
            $image = $form->get('Image')->getData();

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
                
                //*******get the object and the score using google cloud api**************** */
                // Chemin vers votre fichier de clé Google Cloud
                $googleCloudCredentialsPath = __DIR__ . '/../config/google_cloud_credentials.json';
                // Création d'un client ImageAnnotator
                $imageAnnotator = new ImageAnnotatorClient([
                    'credentials' => $googleCloudCredentialsPath
                ]);

                // Chemin vers l'image à analyser  
                $imagePath = __DIR__ . '/../../public/contents/uploads/products/'.$newFilename;
                
                // Lecture du contenu de l'image
                $imageContent = file_get_contents($imagePath);

                // Requête de détection d'objets
                $response = $imageAnnotator->objectLocalization($imageContent);
                
                // Récupération des annotations d'objets
                $objects = $response->getLocalizedObjectAnnotations();
                
                $etiquette = $objects[0]->getName();
                $score = $objects[0]->getScore();
                // Affichage des annotations d'objets
              //  echo $objects[0]->getName() . ' (score : ' . $objects[0]->getScore() . ')' . PHP_EOL;
                /*foreach ($objects as $object) {
                    echo $object->getName() . ' (score : ' . $object->getScore() . ')' . PHP_EOL;
                }*/
                
                // Fermeture du client ImageAnnotator
                $imageAnnotator->close();
                //*******END: get the object and the score using google cloud api**************** */
                
            }
            //***************************** */
            $products = $produitRepository->searchProductByImage($etiquette, $score);
            //dd($products);
            /*if($products == null){
                $products = $produitRepository->searchSimilairesProductByImage($etiquette);
            }*/
            
            //return $this->redirectToRoute("app_dash_admin_products");
            return $this->renderForm('front/user-products-list.html.twig', [
                'controller_name' => 'FrontController',
                'title' => 'Zero Waste',
                'products' => $products,
                'totalCommandes' => $totalCommandes,
                'user' => $user,
                'categories' => $categories,
                'searchProdoctForm' => $form,
                'notifications' => $notifications,
            ]);
        }

        return $this->renderForm('front/user-products-list.html.twig', [
            'controller_name' => 'FrontController',
            'title' => 'Zero Waste',
            'products' => $products,
            'totalCommandes' => $totalCommandes,
            'user' => $user,
            'categories' => $categories,
            'searchProdoctForm' => $form,
            'notifications' => $notifications,
        ]);

    }

    #[Route('/dash/admin/products', name: 'app_dash_admin_products')]
    public function productsList(ProduitRepository $produitRepository)
    {
        $userFullname = "Braiek Ali";
        $products = $produitRepository->findAll();
        
        return $this->renderForm('dash_admin/dash-admin-products.html.twig', array(
            'userFullname' => $userFullname,
            'title' => 'Zero Waste',
            'products' => $products,
    ));

    }

    #[Route('/dash/admin/products/{id}', name: 'deleteProduit')]
    public function deleteProduit(ManagerRegistry $repo, $id): Response
    {

        $product = $repo->getRepository(Produit::class)->find($id);
        $em = $repo->getManager();
        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('app_dash_admin_products');
    }

    #[Route('/dash/admin/products/deleteCat/{id}', name: 'deleteCategorie')]
    public function deleteCategorie(ManagerRegistry $repo, $id): Response
    {

        $categorie = $repo->getRepository(CategorieProduit::class)->find($id);
        $em = $repo->getManager();
        $em->remove($categorie);
        $em->flush();

        return $this->redirectToRoute('app_dash_admin_categories');
    }




    #[Route('/dash/admin/products-add/', name: 'app_dash_admin_products_add')]
    public function dashProductsAdd(Request $request, ManagerRegistry $doctrine, CategorieProduitRepository $categorieProduitRepository, SluggerInterface $slugger): Response
    {
        $userFullname = "Braiek Ali";
        $categories = $categorieProduitRepository->findAll();

        $product = new Produit();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();

            //******************************* */
            $image = $form->get('Image')->getData();

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
                $product->setImage($newFilename);
            }
            //***************************** */
            //*******get the object and the score using google cloud api**************** */
            // Chemin vers votre fichier de clé Google Cloud
            $googleCloudCredentialsPath = __DIR__ . '/../config/google_cloud_credentials.json';
            // Création d'un client ImageAnnotator
            $imageAnnotator = new ImageAnnotatorClient([
                'credentials' => $googleCloudCredentialsPath
            ]);

            // Chemin vers l'image à analyser  
            $imagePath = __DIR__ . '/../../public/contents/uploads/products/'.$newFilename;
            
            // Lecture du contenu de l'image
            $imageContent = file_get_contents($imagePath);

            // Requête de détection d'objets
            $response = $imageAnnotator->objectLocalization($imageContent);
            
            // Récupération des annotations d'objets
            $objects = $response->getLocalizedObjectAnnotations();
            
            $product->setEtiquette($objects[0]->getName());
            $product->setScore($objects[0]->getScore());
            // Affichage des annotations d'objets
            echo $objects[0]->getName() . ' (score : ' . $objects[0]->getScore() . ')' . PHP_EOL;
            /*foreach ($objects as $object) {
                echo $object->getName() . ' (score : ' . $object->getScore() . ')' . PHP_EOL;
            }*/
            
            // Fermeture du client ImageAnnotator
            $imageAnnotator->close();
            //*******END: get the object and the score using google cloud api**************** */
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute("app_dash_admin_products");
        }
        return $this->renderForm('dash_admin/dash-admin-product-add.html.twig', [
            'title' => 'Zero Waste',
            'userFullname' => $userFullname,
            'categories' => $categories,
            "formProduct" => $form,
        ]);
    }

    #[Route('/dash/admin/product-Update/{id}', name: 'editProduit')]
    public function dashProductsUpdate(Request $request,$id, ManagerRegistry $doctrine, CategorieProduitRepository $categorieProduitRepository, SluggerInterface $slugger): Response
    {
        $userFullname = "Braiek Ali";

        $product = $doctrine->getRepository(Produit::class)->find($id);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();

            //******************************* */
            $image = $form->get('Image')->getData();

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
                $product->setImage($newFilename);
                //*******get the object and the score using google cloud api**************** */
                // Chemin vers votre fichier de clé Google Cloud
                $googleCloudCredentialsPath = __DIR__ . '/../config/google_cloud_credentials.json';
                // Création d'un client ImageAnnotator
                $imageAnnotator = new ImageAnnotatorClient([
                    'credentials' => $googleCloudCredentialsPath
                ]);

                // Chemin vers l'image à analyser  
                $imagePath = __DIR__ . '/../../public/contents/uploads/products/'.$newFilename;
                
                // Lecture du contenu de l'image
                $imageContent = file_get_contents($imagePath);

                // Requête de détection d'objets
                $response = $imageAnnotator->objectLocalization($imageContent);
                
                // Récupération des annotations d'objets
                $objects = $response->getLocalizedObjectAnnotations();
                
                $product->setEtiquette($objects[0]->getName());
                $product->setScore($objects[0]->getScore());
                // Affichage des annotations d'objets
                echo $objects[0]->getName() . ' (score : ' . $objects[0]->getScore() . ')' . PHP_EOL;
                /*foreach ($objects as $object) {
                    echo $object->getName() . ' (score : ' . $object->getScore() . ')' . PHP_EOL;
                }*/
                
                // Fermeture du client ImageAnnotator
                $imageAnnotator->close();
                //*******END: get the object and the score using google cloud api**************** */
                
            }
            //***************************** */
            
            $em->flush();
            return $this->redirectToRoute("app_dash_admin_products");
        }
        return $this->renderForm('dash_admin/dash-admin-product-update.html.twig', [
            'title' => 'Zero Waste',
            'userFullname' => $userFullname,
            "formProduct" => $form,
            "product" =>$doctrine->getRepository(Produit::class)->find($id),

        ]);
    }



    //************************************************************************** */
    #

    /************************************************************************ */
  

    #[Route('products/filterCategories', name: 'filterCategories')]
    public function filterCategories(ProduitRepository $produitRepository, Request $request, NormalizerInterface $Normalizer): Response
    {
        $id =$request->get('categoryIdData');
        //$id = "37";
       // var_dump($id);// kan theb tasti chouf l console , kan temchi lel lien mtaa func hedhi taw tel9aha dima null
        $products = $produitRepository->filterCategories($id);
       // $products = $produitRepository->findAll();
        
        
        $json = $Normalizer->normalize($products, 'json', ['groups' => 'produit_group']);

        $jsonString = json_encode($json);
        //var_dump($jsonString); // hedhi taamel erreur qui tji tasti biha 3leh allahou a3lam 😅
        return new Response($jsonString);
    }



    #[Route('products/trieCategories', name: 'trieCategories')]
    public function trieCategories(ProduitRepository $produitRepository,ManagerRegistry $doctrine, Request $request, NormalizerInterface $Normalizer): Response
    {
        $data =$request->get('categOrderData');
        
       $entityManager = $doctrine->getManager();
      
       foreach ($data as $item) {
           $categorie = $doctrine->getRepository(CategorieProduit::class)->find($item['id']);
   
           $categorie->setOrderCateg($item['categOrder']);
           $entityManager->persist($categorie);
       }
   
       $entityManager->flush();
   
       return new Response('updated!');
    }


    #[Route('/dash/admin/products/offre/{id}', name: 'offreProduit')]
    public function offreProduit(ProduitRepository $produitRepository, ManagerRegistry $repo, $id, Request $request, HubInterface $hub): Response
    {
        $userFullname = "Braiek Ali";
        $products = $produitRepository->findAll();

        $product = $repo->getRepository(Produit::class)->find($id);

        $form = $this->createForm(OffreType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $repo->getManager();
            $em->flush();
            $date = new DateTime();
            
            //-----------------------notification
            $update = new Update(
                'https://example.com/books/1',
                json_encode([
                    'status' => 'New Product Offer!',
                    'id' => $id,
                    'image' => $product->getImage(),
                    'nomProduit' => $product->getNomProduit(),
                    'date' => $date,
                    ])
            );
    
            $hub->publish($update);
    
            //return new Response('published!');
            //-----------------------------------
            //add the notification to the database
            $notif = new Notification();
            $notif->setContent("New Product Offer!");
            $date = new DateTime();
            $notif->setDate($date);
            $notif->setProduct($product);
            $em->persist($notif);
            $em->flush();

            
            return $this->redirectToRoute("app_dash_admin_products");
        }
        return $this->renderForm('dash_admin/dash-admin-products-offre.html.twig', array(
            'userFullname' => $userFullname,
            'title' => 'Zero Waste',
            'products' => $products,
            "formOffre" => $form,
        ));
    }


    #[Route('/product-one/{id}', name: 'product-one')]
    public function productOne(ProduitRepository $produitRepository, $id, CommandsRepository $commandsRepository, ManagerRegistry $doct,  CommandsProduitRepository $commandsProduitRepository, ProductFavorisRepository $productFavorisRepository): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
       
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);
        if($commande != null)
        {
            $totalCommandes = $commandsProduitRepository->getCommandesNumber($commande->getId());
        }else{
            $totalCommandes = 0;
        }
        
        $product = $produitRepository->find($id);

        $productFav = $productFavorisRepository->findOneBy(["user" => $user->getId(), "product" => $id]);
        //dd($productFav);

        return $this->render('front/user-product-one.html.twig', [
            'title' => 'Zero Waste',
            'product' => $product,
            'totalCommandes' => $totalCommandes,
            'user' => $user,
            'productFav' => $productFav,
        ]);
    }

    #[Route('/productsFavList', name: 'app_productsFav')]
    public function productsFav(ProduitRepository $produitRepository, CommandsRepository $commandsRepository, ManagerRegistry $doct,  CommandsProduitRepository $commandsProduitRepository, ProductFavorisRepository $productFavorisRepository): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
       
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);
        if($commande != null)
        {
            $totalCommandes = $commandsProduitRepository->getCommandesNumber($commande->getId());
        }else{
            $totalCommandes = 0;
        }
        
        $productsFav = $user->getProductFavoris();
        //$products = $productFavorisRepository->findBy(["user" => $user->getId()]);
        
        //dd($products);
        
        return $this->render('front/user-products-favoris.html.twig', [
            'title' => 'Zero Waste',
            'productsFav' => $productsFav,
            'totalCommandes' => $totalCommandes,
            'user' => $user,
        ]);

    }


    #[Route('/productsFavAdd', name: 'app_productsFavAdd')]
    public function productsFavAdd(ManagerRegistry $doct, Request $request, ProductFavorisRepository $productFavorisRepository): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
       
        
        $id =$request->get('productIdData');

        $product= $doct->getRepository(Produit::class)->find($id);
        
        $productFav = $productFavorisRepository->findOneBy(["user" => $user->getId(), "product" => $id]);
        $em = $doct->getManager();
        if($productFav == null){
            $productFavoris = new ProductFavoris();
            $productFavoris->setUser($user);
            $productFavoris->setProduct($product);

            $em->persist($productFavoris);
        }else{
            $em->remove($productFav);
        }
        $em->flush();
        
        return new Response('updated!');
    }

    
    #[Route('/facebook_share', name: 'facebook_share')]
    public function facebook_share(ManagerRegistry $doct, Request $request): Response
    {
        $id =$request->get('productIdData');
        $product= $doct->getRepository(Produit::class)->find($id);

        

        $fb = new Facebook([
            'app_id' => '162589433299922',
            'app_secret' => '8951380abcab7557b3c9fe51303dde33',
            'default_graph_version' => 'v16.0',
            'default_access_token' => 'EAACT381qg9IBAMTogYd8cvuZBcuXruqGfBGq1ZB2n203ptbr7V31k1ggjSjYdK0OlsNGQQPIFDChjSwZB4WjkMehOx3keeKVnEpfEpJ6ELLBicWMuueO4wMW0dg6ZBMQqkOchl7ZC2ik3JtdOq31hwexZCUOV7O3nDzTjIDmKnPyE9VTMXIcDLvz0TauwjXQ1xbnQ8RMK0WYZBiJcHomcZCA',
        ]);
        
        $productUrl = 'https://127.0.0.1:8000/product-one/48';
        //pour envoyer une image changer feed par photos
        //  $imageUrl = 'https://scontent.ftun9-1.fna.fbcdn.net/v/t39.30808-6/334954366_866719021098542_7251173562485808604_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=730e14&_nc_ohc=vfw_82p3lmAAX_co5Bd&_nc_ht=scontent.ftun9-1.fna&oh=00_AfBAxE4r48haqlt5XdDtkrOfWvNxpFxP1vrAhs5uvOZjxA&oe=640D0C1A';
        
        //  $img = 'https://drive.google.com/file/d/1i9TP4fkMNKBHblB3jFhbguH0ANzMmPt7/view';
        /*  $data = [
            'caption' => $product->getNomProduit(),
            'url' => $imageUrl,
            'link' => $productUrl,
            'additional_text' => 'Cliquez ici pour en savoir plus : https://127.0.0.1:8000/product-one/48'
            
            
        ];
        */
        $message ='a new product is available on ZeroWaste: '. '                                                                ' 
                    .'Name: '.$product->getNomProduit() . '                                                                                 '  
                    .'**Description: '. $product->getDescription() . '                                                                                        ' 
                    .'**Price: '. $product->getPrixProduit() . '                                                                                                                                         ' 
                    .'**Points: '. $product->getPrixPointProduit();
        $data = [
            'message' => $message ,
            'link' => sprintf('https://127.0.0.1:8000/product-one/%d', $product->getId()),
        ];
        
        $response = $fb->post('/me/feed', $data);
        //  dd($response);
        // return $this->redirectToRoute("product-one");
        
        if ($response->isError()) {
            // handle error
            
        } else {
            //return $this->redirectToRoute("product-one");
            // post was shared successfully
        }
        
        return new Response('updated!');
    }

    #[Route('products/searchProduct', name: 'searchProduct')]
    public function searchProduct(ProduitRepository $produitRepository, Request $request, NormalizerInterface $Normalizer): Response
    {
        $value =$request->get('searchProductData');
        $products = $produitRepository->searchProductFunction($value);        
        
        $json = $Normalizer->normalize($products, 'json', ['groups' => 'produit_group']);

        $jsonString = json_encode($json);

        return new Response($jsonString);
    }

    


   
    /************************************recherche par image****************************************/
    #[Route('/ImageSearch', name: 'ImageSearch')]
    public function ImageSearch(ManagerRegistry $doct, Request $request): Response
    {
        // Chemin vers votre fichier de clé Google Cloud
        $googleCloudCredentialsPath = __DIR__ . '/../config/google_cloud_credentials.json';
        // Création d'un client ImageAnnotator
        $imageAnnotator = new ImageAnnotatorClient([
            'credentials' => $googleCloudCredentialsPath
        ]);

        // Chemin vers l'image à analyser
        $imagePath1 = __DIR__ . '/../../public/contents/img/b13.jpg';
        $imagePath2 = __DIR__ . '/../../public/contents/img/b13.jpg';

        $imageContent1 = file_get_contents($imagePath1);
        $imageContent2 = file_get_contents($imagePath2);
        
        // Chemin vers l'image à analyser
        $imagePath = __DIR__ . '/../../public/contents/img/green.webp';
        
        // Lecture du contenu de l'image
        $imageContent = file_get_contents($imagePath);
        
        // Requête de détection d'objets
        $response = $imageAnnotator->objectLocalization($imageContent);
        
        // Récupération des annotations d'objets
        $objects = $response->getLocalizedObjectAnnotations();
        
        // Affichage des annotations d'objets
        echo $objects[0]->getName() . ' (score : ' . $objects[0]->getScore() . ')' . PHP_EOL;
        /*foreach ($objects as $object) {
            echo $object->getName() . ' (score : ' . $object->getScore() . ')' . PHP_EOL;
        }*/
        
        // Fermeture du client ImageAnnotator
        $imageAnnotator->close();
        return new Response('updated!');
    }



    #[Route('/NotifMarkAsRead/{id}', name: 'NotifMarkAsRead')]
    public function NotifMarkAsRead($id, UserNotificationRepository $userNotif,  NotificationRepository $notif , ManagerRegistry $doct, Request $request, ProductFavorisRepository $productFavorisRepository): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
       

        $notification = $notif->find($id);

        $userNotification = new UserNotification();
        $userNotification->setUser($user);
        $userNotification->setNotification($notification);
        $userNotification->setMarkAsread(1);
        
        $em = $doct->getManager();
        $em->persist($userNotification);
        $em->flush();
        
        
        return $this->redirectToRoute("product-one",['id' => $notification->getProduct()->getId()]);
    }



    #[Route('/userNotificationsGoTo/', name: 'userNotificationsGoTo')]
    public function userNotificationsGoTo(  NotificationRepository $notif ,ManagerRegistry $doct, Request $request): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
       
        $notifId =$request->get('notifId');

         $notification = $notif->find($notifId);

        $userNotification = new UserNotification();
        $userNotification->setUser($user);
        $userNotification->setNotification($notification);
        $userNotification->setMarkAsread(1);
        
        $em = $doct->getManager();
        $em->persist($userNotification);
        $em->flush();
        
        //dd($notifications);

        
        return new Response('updated!');
    }




 #[Route('/mercure', name: 'mercure')]
 public function mercure(): Response
{
    // Chemin vers votre fichier de clé Google Cloud
    $googleCloudCredentialsPath = __DIR__ . '/../config/google_cloud_credentials.json';
    // Création d'un client ImageAnnotator
    $imageAnnotator = new ImageAnnotatorClient([
        'credentials' => $googleCloudCredentialsPath
    ]);

    // Chemin vers l'image à analyser
    $imagePath = __DIR__ . '/../../public/contents/img/b13.jpg';

    // Lecture du contenu de l'image
    $imageContent = file_get_contents($imagePath);

    // Requête de détection d'objets
    $response = $imageAnnotator->objectLocalization($imageContent);

    // Récupération des annotations d'objets
    $objects = $response->getLocalizedObjectAnnotations();

    // Parcours des annotations d'objets pour trouver une bouteille plastique
    $containsBottle = false;
    foreach ($objects as $object) {
        if ($object->getName() == 'Bottle' && $object->getScore() >= 0.5) {
            $containsBottle = true;
            break;
        }
    }

    // Affichage du résultat de la détection
    if ($containsBottle) {
        echo "L'image contient une bouteille plastique." . PHP_EOL;
    } else {
        echo "L'image ne contient pas de bouteille plastique." . PHP_EOL;
    }

    // Fermeture du client ImageAnnotator
    $imageAnnotator->close();

    return new Response('updated!');
}





    #[Route('/dash/admin/product/categories', name: 'app_dash_admin_categories')]
     public function addcategory(Request $request, ManagerRegistry $doctrine,ProduitRepository $produitRepository, CategorieProduitRepository $categorieProduitRepository, SluggerInterface $slugger)
        {
            $userFullname = "Braiek Ali";
            $products = $produitRepository->findAll();
            $categories = $categorieProduitRepository->findAll();
            
            $categorieProduit = new CategorieProduit();
            $form = $this->createForm(CategorieProduitType::class, $categorieProduit);
            $form->handleRequest($request);
            if ($form->isSubmitted() &&$form->isValid()) {
                $em = $doctrine->getManager();
    
                //******************************* */
                $image = $form->get('imageCategorie')->getData();
    
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
    
                    $categorieProduit->setImageCategorie($newFilename) ;
                }
                //***************************** */
                
    
                $em->persist($categorieProduit);
                $em->flush();
                return $this->redirectToRoute("app_dash_admin_categories");
            }
            return $this->renderForm('dash_admin/dash-admin-products-categories.html.twig', array(
                'userFullname' => $userFullname,
                'title' => 'Zero Waste',
                "formCategorie" => $form,
                'categories' => $categories,
                'products' => $products,
        ));
    
        }


//************************************************************************** */
#

/************************************************************************ */
#[Route('/dash/admin/product/category/update/{id}', name: 'editCategory')]
public function dashCategoryUpdate(Request $request,$id, ManagerRegistry $doctrine,ProduitRepository $produitRepository, CategorieProduitRepository $categorieProduitRepository, SluggerInterface $slugger): Response
{
    $userFullname = "Braiek Ali";
    $products = $produitRepository->findAll();

    $category = $doctrine->getRepository(CategorieProduit::class)->find($id);

    $form = $this->createForm(CategorieProduitType::class, $category);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();

        //******************************* */
        $image = $form->get('imageCategorie')->getData();
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
            $category->setImageCategorie($newFilename);
        }
        //***************************** */
        $em->flush();
        return $this->redirectToRoute("app_dash_admin_categories");
    }
    return $this->renderForm('dash_admin/dash-admin-product-category-update.html.twig', [
        'title' => 'Zero Waste',
        'userFullname' => $userFullname,
        "formCategorie" => $form,
        "category" => $category,
        'products' => $products,
        

    ]);
}



//stripe
#[Route('/{id}/stripe/create-charge', name: 'app_products_charge', methods: ['POST'])]
public function createCharge($id,ProductsRepository $ProductsRepository, Request $request)
{
    Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
    Stripe\Charge::create ([
            "amount" => 50000,
            "currency" => "usd",
            "source" => $request->request->get('stripeToken'),
            "description" => "Binaryboxtuts Payment Test",
    ]);
    $this->addFlash(
        'success',
        'Payment Successful!'
    );
    return $this->redirectToRoute('app_products_success', ['id' => $id], Response::HTTP_SEE_OTHER);
}

        

}