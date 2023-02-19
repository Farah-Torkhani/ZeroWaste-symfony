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
    public function products(ProduitRepository $produitRepository): Response
    {
        $products = $produitRepository->findAll();
        return $this->render('front/user-products-list.html.twig', [
            'controller_name' => 'FrontController',
            'title' => 'Zero Waste',
            'products' => $products,
        ]);

    }

    /*
    #[Route('/dash/admin/products', name: 'app_dash_admin_products')]
    public function dashProducts(ProduitRepository $produitRepository, CategorieProduitRepository $categorieProduitRepository): Response
    {
        $userFullname = "Braiek Ali";
        $products = $produitRepository->findAll();
        $categories = $categorieProduitRepository->findAll();
        return $this->render('dash_admin/dash-admin-products.html.twig', [
            'title' => 'Zero Waste',
            'userFullname' => $userFullname,
            'products' => $products,
            'categories' => $categories,
        ]);
    }*/

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

        return $this->redirectToRoute('app_dash_admin_products');
    }


   
    #[Route('/dash/admin/products', name: 'app_dash_admin_products')]
    public function addproducts(Request $request, ManagerRegistry $doctrine,ProduitRepository $produitRepository, CategorieProduitRepository $categorieProduitRepository)
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
        /*    $cat = new CategorieProduit();
            $image = $form->get('imageCategorie')->getData();

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
                $cat->setImageCategorie($newFilename) ;
            }*/
            //***************************** */
            

            $em->persist($categorieProduit);
            $em->flush();
            return $this->redirectToRoute("app_dash_admin_products");
        }
        return $this->renderForm('dash_admin/dash-admin-products.html.twig', array(
            'userFullname' => $userFullname,
            'title' => 'Zero Waste',
            "formCategorie" => $form,
            'categories' => $categories,
            'products' => $products,
    ));
        //return $this->render('classroom/add.html.twig', array("formstudent" => $form->createView()));
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


}
