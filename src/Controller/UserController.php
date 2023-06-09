<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ForgotPassType;
use App\Form\ForgotPassword2Type;
use App\Form\UpdateUserType;
use App\Form\UsersFilterType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

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


    #[Route('/forgotPassword', name: 'app_user_forgot_password')]
    public function forgotPassword(Request $request, ManagerRegistry $doct, MailerInterface $mailer, TokenGeneratorInterface  $tokenGenerator): Response
    {
        $userr = $this->getUser();
        if ($userr) {
            if (in_array('ROLE_ADMIN', $userr->getRoles(), true)) {
                return $this->redirectToRoute('app_dash_admin_users');
            } else {
                return $this->redirectToRoute('app_dash_user_home');
            }
        }


        $changePassForm = $this->createForm(ForgotPassType::class);

        $changePassForm->handleRequest($request);
        if ($changePassForm->isSubmitted() && $changePassForm->isValid()) {
            $email = $changePassForm->get('email')->getData();
            $user = $doct->getRepository(User::class)->findOneBy(['email' => $email]);
            if (!$user) {
                $this->addFlash('warning', 'email not associated with zeroWaste account');
                return $this->redirectToRoute("app_login");
            }
            if (!$user->getIsVerified()) {
                $this->addFlash('warning', 'Please verify your email first');
                return $this->redirectToRoute("app_login");
            }
            if (!$user->getState()) {
                $this->addFlash('warning', 'Your account is blocked');
                return $this->redirectToRoute("app_login");
            }

            $token = $tokenGenerator->generateToken();
            $user->setToken($token);

            $em = $doct->getManager();
            $em->flush();

            $url = $this->generateUrl('app_user_forgotPasswordVerif', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
            $email = (new TemplatedEmail())
                ->from('Contact@zerowaste.com')
                ->to($user->getEmail())
                ->subject('Reset password request')
                ->htmlTemplate('registration/mailTemplate.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'url' => $url,
                    'emailTitle' => 'Reset password request',
                    'emailDesc' => 'It seems like you forgot your password. If this is true, click the button below to reset your password:',
                    'btnTitle' => 'Reset my password',
                ]);

            $mailer->send($email);
            $this->addFlash('success', 'A reset password request was sent to you');
            return $this->redirectToRoute("app_login");
        }

        return $this->render('security/forgotPass.html.twig', [
            'title' => 'Zero Waste',
            'changePassForm' => $changePassForm->createView(),
        ]);
    }


    #[Route('/forgotPasswordVerif/{token}', name: 'app_user_forgotPasswordVerif')]
    public function forgotPasswordVerif(Request $request, ManagerRegistry $doct, TokenGeneratorInterface  $tokenGenerator, UserPasswordHasherInterface $passwordHasher, $token): Response
    {
        $userr = $this->getUser();
        if ($userr) {
            if (in_array('ROLE_ADMIN', $userr->getRoles(), true)) {
                return $this->redirectToRoute('app_dash_admin_users');
            } else {
                return $this->redirectToRoute('app_dash_user_home');
            }
        }


        $user = $doct->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            $this->addFlash('warning', 'error:token expired');
            return $this->redirectToRoute("app_login");
        }

        $changePassForm = $this->createForm(ForgotPassword2Type::class);

        $changePassForm->handleRequest($request);
        if ($changePassForm->isSubmitted() && $changePassForm->isValid()) {
            $token = $tokenGenerator->generateToken();
            $user->setToken($token);
            $hashedPassword = $passwordHasher->hashPassword($user, $changePassForm->get('password')->getData());
            $user->setPassword($hashedPassword);


            $em = $doct->getManager();
            $em->flush();

            $this->addFlash('success', 'password changed successfully');
            return $this->redirectToRoute("app_login");
        }




        return $this->render('security/forgotPassUpdate.html.twig', [
            'title' => 'Zero Waste',
            'changePassForm' => $changePassForm->createView(),
        ]);
    }



    //**************admin side ******************************/
    #[Route('/dash/admin/users', name: 'app_dash_admin_users')]
    public function dashAdminUsers(Request $request, ManagerRegistry $doct, UserRepository $userRepo): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $users = $doct->getRepository(User::class)->findAll();
        // $users = $userRepo->findByRole('["ROLE_USER"]');
        // $associationList = $userRepo->findByRole("ROLE_ASSOCIATION");

        $filterForm = $this->createForm(UsersFilterType::class);
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $type = $filterForm->get('role')->getData();
            if ($type == 'all') {
                $users = $doct->getRepository(User::class)->findAll();
            } elseif ($type == '["ROLE_USER"]') {
                $users = $userRepo->findByRole('["ROLE_USER"]');
            } else {
                $users = $userRepo->findByRole('["ROLE_ASSOCIATION"]');
            }
        }


        return $this->render('dash_admin/dash-admin-users.html.twig', [
            'title' => 'Zero Waste',
            'user' => $user,
            'usersList' => $users,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    #[Route('/dash/admin/users/block/{id}', name: 'app_dash_admin_users_block')]
    public function dashAdminUserBlock(Request $request, ManagerRegistry $doct, $id): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['id' => $id]);
        if ($user) {
            $user->setState(false);
            $em = $doct->getManager();
            $em->flush();
            $this->addFlash('success', 'user blocked');

            return $this->redirectToRoute('app_dash_admin_users');
        } else {
            $this->addFlash('warning', 'user not found');
            return $this->redirectToRoute('app_dash_admin_users');
        }
    }

    #[Route('/dash/admin/users/unblock/{id}', name: 'app_dash_admin_users_unblock')]
    public function dashAdminUserUnblock(Request $request, ManagerRegistry $doct, $id): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['id' => $id]);
        if ($user) {
            $user->setState(true);
            $em = $doct->getManager();
            $em->flush();
            $this->addFlash('success', 'user unblocked');

            return $this->redirectToRoute('app_dash_admin_users');
        } else {
            $this->addFlash('warning', 'user not found');
            return $this->redirectToRoute('app_dash_admin_users');
        }
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
                $updatedUser->setImgUrl($newFilename);
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

    #[Route('/dash/admin/home', name: 'app_dash_admin_home')]
    public function dashAdminHome(Request $request, ManagerRegistry $doct, ChartBuilderInterface $chartBuilder, UserRepository $userRepo): Response
    {
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        $nbIndiv = count($userRepo->findByRole('["ROLE_USER"]'));
        $nbAssocitiation = count($userRepo->findByRole('["ROLE_ASSOCIATION"]'));

        $nbActiveUser = count($userRepo->findByState(true));
        $nbUnactiveeUser = count($userRepo->findByState(false));

        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => ['Individuals', 'Association'],
            'datasets' => [
                [
                    'label' => 'number of users is',
                    'backgroundColor' => '#5BA890',
                    'borderColor' => '#43882B',
                    'data' => [$nbIndiv, $nbAssocitiation],
                ],
            ],
        ]);



        $chart2 = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart2->setData([
            'labels' => ['Active', 'Unactive'],
            'datasets' => [
                [
                    'backgroundColor' => ['#5BA890', '#FF1E00'],
                    // 'borderColor' => '#43882B',
                    'data' => [$nbActiveUser, $nbUnactiveeUser],
                ],

            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 50,
                ],
            ],
        ]);



        return $this->render('dash_admin/dash-admin-home.html.twig', [
            'title' => 'Zero Waste',
            'user' => $user,
            'chart' => $chart,
            'chart2' => $chart2,
        ]);
    }
}
