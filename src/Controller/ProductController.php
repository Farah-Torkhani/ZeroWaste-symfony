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
    public function products(ProduitRepository $produitRepository, CommandsRepository $commandsRepository, ManagerRegistry $doct,  CommandsProduitRepository $commandsProduitRepository,  CategorieProduitRepository $categorieProduitRepository): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
       
        $commande = $commandsRepository->findOneBy(["user" => $user->getId(), "status" => 0]);
        if($commande != null)
        {
            $totalCommandes = $commandsProduitRepository->getCommandesNumber($commande->getId());
        }else{
            $totalCommandes = 0;
        }
        
        $products = $produitRepository->findAll();
        //$categories = $categorieProduitRepository->findAll();
        $categories = $categorieProduitRepository->getAllCategoriesSortedByOrderCateg();

        return $this->render('front/user-products-list.html.twig', [
            'controller_name' => 'FrontController',
            'title' => 'Zero Waste',
            'products' => $products,
            'totalCommandes' => $totalCommandes,
            'user' => $user,
            'categories' => $categories,
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


   
    #[Route('/dash/admin/product/categories', name: 'app_dash_admin_categories')]
    public function addcategory(Request $request, ManagerRegistry $doctrine,ProduitRepository $produitRepository, CategorieProduitRepository $categorieProduitRepository, SluggerInterface $slugger)
    {
        $userFullname = "Braiek Ali";
        $products = $produitRepository->findAll();
       // $categories = $categorieProduitRepository->findAll();
       $categories = $categorieProduitRepository->getAllCategoriesSortedByOrderCateg();
        
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
            
            //-----------------------notification
            $update = new Update(
                'https://example.com/books/1',
                json_encode(['status' => 'msg reçu'])
            );
    
            $hub->publish($update);
    
            //return new Response('published!');
            //-----------------------------------
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
 //       return $this->redirectToRoute("product-one");
        
        if ($response->isError()) {
            // handle error
            
        } else {
            //return $this->redirectToRoute("product-one");
            // post was shared successfully
        }
        
        return new Response('updated!');
    }

   
    


        
        

}
