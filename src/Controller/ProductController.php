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
        $categories = $categorieProduitRepository->findAll();
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

}
