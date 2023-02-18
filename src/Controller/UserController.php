<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UpdateUserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    #[Route('/dash/user/profile', name: 'app_dash_user_profile')]
    public function dashUserProfile(Request $request, ManagerRegistry $doct, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $changePassForm = $this->createForm(ChangePasswordType::class);

        $changePassForm->handleRequest($request);
        if ($changePassForm->isSubmitted() && $changePassForm->isValid()) {
            $oldPass = $changePassForm->get('oldPassword')->getData();
            if ($passwordHasher->isPasswordValid($user, $oldPass)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $changePassForm->get('password')->getData());
                $user->setPassword($hashedPassword);
                $em = $doct->getManager();
                $em->flush();

                $this->addFlash('success', 'password changed successfully');
                return $this->redirectToRoute("app_dash_user_profile");
            } else {
                $this->addFlash('warning', 'Please verify your password');
                return $this->redirectToRoute("app_dash_user_profile");
            }
        }

        return $this->render('dash_user/dash-user-profile.html.twig', [
            'title' => 'Zero Waste',
            'user' => $user,
            'changePassForm' => $changePassForm->createView(),
        ]);
    }



    #[Route('/dash/user/profileUpdate', name: 'app_dash_user_profile_update')]
    public function dashUserProfileUpdate(Request $request, ManagerRegistry $doct, SluggerInterface $slugger): Response
    {
        $user  = $this->getUser();
        $email = $user->getUserIdentifier();
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $email]);

        $updateForm = $this->createForm(UpdateUserType::class, $user);
        $updateForm->handleRequest($request);

        if ($updateForm->isSubmitted() && $updateForm->isValid()) {


            $userPic = $updateForm->get('ProfilePic')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($userPic) {
                $originalFilename = pathinfo($userPic->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $userPic->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $userPic->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setImgUrl($newFilename);
            }





            $em = $doct->getManager();
            $em->flush();


            $this->addFlash('success', 'Data Updated');
            return $this->redirectToRoute("app_dash_user_profile");
        }


        return $this->render('dash_user/dash-user-profile-update.html.twig', [
            'title' => 'Zero Waste',
            'userFullname' => $email,
            'user' => $user,
            'registreForm' => $updateForm->createView(),
        ]);
    }



    //**************admin side ******************************/
    #[Route('/dash/admin/users', name: 'app_dash_admin_users')]
    public function dashAdminUsers(Request $request, ManagerRegistry $doct, UserRepository $userRepo): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $users = $doct->getRepository(User::class)->findAll();
        // $indivList = $userRepo->findByRole("ROLE_USER");
        // $associationList = $userRepo->findByRole("ROLE_ASSOCIATION");


        return $this->render('dash_admin/dash-admin-users.html.twig', [
            'title' => 'Zero Waste',
            'user' => $user,
            'usersList' => $users,
        ]);
    }


    #[Route('/dash/admin/users/update/{id}', name: 'app_dash_admin_users_update')]
    public function dashAdminUsersUpdate(Request $request, ManagerRegistry $doct, UserRepository $userRepo, SluggerInterface $slugger, $id): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $updatedUser = $doct->getRepository(User::class)->find($id);
        // $indivList = $userRepo->findByRole("ROLE_USER");
        // $associationList = $userRepo->findByRole("ROLE_ASSOCIATION");

        $updateForm = $this->createForm(UpdateUserType::class, $updatedUser);
        $updateForm->handleRequest($request);

        if ($updateForm->isSubmitted() && $updateForm->isValid()) {


            $userPic = $updateForm->get('ProfilePic')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($userPic) {
                $originalFilename = pathinfo($userPic->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $userPic->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $userPic->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setImgUrl($newFilename);
            }





            $em = $doct->getManager();
            $em->flush();


            $this->addFlash('success', 'Data Updated');
            return $this->redirectToRoute("app_dash_admin_users");
        }



        return $this->render('dash_admin/dash-admin-users-update.html.twig', [
            'title' => 'Zero Waste',
            'user' => $user,
            'updatedUser' => $updatedUser,
            'registreForm' => $updateForm->createView(),

        ]);
    }
}