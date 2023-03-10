<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserApiController extends AbstractController
{
    #[Route('/user/api', name: 'app_user_api')]
    public function index(): Response
    {
        return $this->render('user_api/index.html.twig', [
            'controller_name' => 'UserApiController',
        ]);
    }



    #[Route('/user/api/signUp', name: 'app_user_api_signUp')]
    public function signupAction(Request $request, ManagerRegistry $doct, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer, TokenGeneratorInterface  $tokenGenerator,)
    {
        $fullname = $request->query->get("fullname");
        $email = $request->query->get("email");
        $tel = $request->query->get("tel");
        $password = $request->query->get("password");
        $roles = $request->query->get("roles");


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new Response("email invalid.");
        }

        if ($roles != "ROLE_ASSOCIATION" && $roles != "ROLE_USER") {
            return new Response("warning: what are you doing little hacker 🐱‍💻👩‍💻");
        }

        if (!ctype_digit($tel)) {
            return new Response("invalid tel.");
        }


        $user = new User();
        $user->setFullName($fullname);
        $user->setEmail($email);
        $user->setTel($tel);
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles(array($roles));
        $user->setImgUrl("defaultPic.jpg");
        $token = $tokenGenerator->generateToken();
        $user->setToken($token);
        //$user->setIsVerified(true);


        try {
            $em = $doct->getManager();
            $em->persist($user);
            $em->flush();

            //send mail
            $url = $this->generateUrl('app_user_api_emailVerif', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
            $email = (new TemplatedEmail())
                ->from('Contact@zerowaste.com')
                ->to($user->getEmail())
                ->subject('Confirm your email address for zeroWaste')
                ->htmlTemplate('registration/mailTemplate.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'url' => $url,
                    'emailTitle' => 'Confirm Your Email Address',
                    'emailDesc' => 'Please confirm your email address by clicking the button below:',
                    'btnTitle' => 'Confirm Email',
                ]);

            $mailer->send($email);

            return new JsonResponse("Account is created, Please confirm your email", 200);
        } catch (\Exception $ex) {
            return new Response("exception: " . $ex->getMessage());
        }


        // http://127.0.0.1:8000/user/api/signUp?fullname=foulen%20benfoulen&email=foulen@gmail.com&tel=25147963&password=12345678&roles=ROLE_USER
    }


    #[Route('/user/api/emailVerif/{token}', name: 'app_user_api_emailVerif')]
    public function emailVerif(ManagerRegistry $doct, TokenGeneratorInterface  $tokenGenerator, $token): Response
    {

        $user = $doct->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            return new Response("warning: error");
        }

        $user->setIsVerified(true);
        $token = $tokenGenerator->generateToken();
        $user->setToken($token);
        $em = $doct->getManager();
        $em->flush();

        return new Response("success: email verified successfully");
    }

    #[Route('/user/api/login', name: 'app_user_api_login')]
    public function loginAction(ManagerRegistry $doct, Request $request, SerializerInterface $serializer, UserPasswordHasherInterface $passwordHasher)
    {
        $email = $request->query->get("email");
        $password = $request->query->get("password");

        $user = $doct->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {

            // if (!$user->getState()) {
            //     return new Response("Your account is blocked.");
            // }
            // if (!$user->getIsVerified()) {
            //     return new Response("Your email is not verified.");
            // }


            if ($passwordHasher->isPasswordValid($user, $password)) {
                // $json = $serializer->serialize($user, "json");
                // $user->setPassword($password);
                $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($user);
                return new JsonResponse($formatted);
            } else {
                return new Response("invalid credentials");
            }
        } else {
            return new Response("user not found");
        }


        // http://127.0.0.1:8000/user/api/login?email=foulen@gmail.com&password=12345678

    }

    #[Route('/user/api/updateProfile', name: 'app_user_api_updateProfile')]
    public function updateProfileApi(Request $request, ManagerRegistry $doct, SluggerInterface $slugger)
    {
        $fullname = $request->query->get("fullname");
        $email = $request->query->get("email");
        $tel = $request->query->get("tel");
        $description = $request->query->get("description");
        // $fbLink = $request->query->get("fbLink");
        // $instaLink = $request->query->get("instaLink");
        // $twitterLink = $request->query->get("twitterLink");

        $user = $doct->getRepository(User::class)->findOneBy(['email' => $email]);


        $user->setFullName($fullname);
        $user->setTel($tel);
        $user->setDescription($description);
        // $user->setFbLink($fbLink);
        // $user->setTwitterLink($twitterLink);
        // $user->setInstaLink($instaLink);


        try {
            $em = $doct->getManager();
            $em->flush();

            return new JsonResponse("Account updated", 200);
        } catch (\Exception $ex) {
            return new Response("exception: " . $ex->getMessage());
        }


        // http://127.0.0.1:8000/user/api/updateProfile?fullname=foulen%20benfoulen&email=foulen@gmail.com&tel=25147963&description=teststest&fbLink=&instaLink=&twitterLink=
    }


    #[Route('/user/api/profileImg', name: 'app_user_api_img')]
    public function viewImg(ManagerRegistry $doct, Request $request, SerializerInterface $serializer, UserPasswordHasherInterface $passwordHasher)
    {
        $id = $request->query->get("id");

        $user = $doct->getRepository(User::class)->findOneBy(['id' => $id]);

        if ($user) {
            $webPath = $this->getParameter('kernel.project_dir') . '/public/assets/uploads/img/' . $user->getImgUrl();
            $response = new BinaryFileResponse($webPath);
            return $response;
        } else {
            return new Response("Error: user not found");
        }

        // http://127.0.0.1:8000/user/api/profileImg?id=8

    }


    #[Route('/user/api/getUser', name: 'app_user_api_getUser')]
    public function getOneUser(ManagerRegistry $doct, Request $request)
    {
        $id = $request->query->get("id");

        $user = $doct->getRepository(User::class)->findOneBy(['id' => $id]);

        if ($user) {
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($user);
            return new JsonResponse($formatted);
        } else {
            return new Response("user not found");
        }


        // http://127.0.0.1:8000/user/api/getUser?id=24

    }

    #[Route('/user/api/getAllUser', name: 'app_user_api_getAllUser')]
    public function getAllUser(ManagerRegistry $doct, Request $request)
    {

        $user = $doct->getRepository(User::class)->findAll();

        if ($user) {
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($user);
            return new JsonResponse($formatted);
        } else {
            return new Response("user not found");
        }


        // http://127.0.0.1:8000/user/api/getAllUser

    }

    #[Route('/user/api/blockUser', name: 'app_user_api_blockUser')]
    public function blockUser(ManagerRegistry $doct, Request $request): Response
    {
        $id = $request->query->get("id");
        $user = $doct->getRepository(User::class)->findOneBy(['id' => $id]);

        if (!$user) {
            return new Response("error: user not found");
        }

        $user->setState(false);
        $em = $doct->getManager();
        $em->flush();

        return new Response("success: user blocked");
    }

    #[Route('/user/api/unblockUser', name: 'app_user_api_unblockUser')]
    public function unblockUser(ManagerRegistry $doct, Request $request): Response
    {
        $id = $request->query->get("id");
        $user = $doct->getRepository(User::class)->findOneBy(['id' => $id]);

        if (!$user) {
            return new Response("error: user not found");
        }

        $user->setState(true);
        $em = $doct->getManager();
        $em->flush();

        return new Response("success: user unblocked");
    }






    #[Route('/user/api/updateProfileImage', name: 'app_user_api_updateProfileImage')]
    public function updateProfileImageApi(Request $request, ManagerRegistry $doct, SluggerInterface $slugger)
    {
        $id = $request->query->get("id");


        $user = $doct->getRepository(User::class)->findOneBy(['id' => $id]);

        if (!$user) {
            return new JsonResponse("User not found", 404);
        }


        $userPic = $request->files->get("profilePic");
        if ($userPic) {
            $originalFilename = pathinfo($userPic->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $userPic->guessExtension();
            // return new JsonResponse("Image added successfully:" . $newFilename, 200);

            // Move the file to the directory where brochures are stored
            try {
                $userPic->move(
                    $this->getParameter('brochures_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
                // return new Response("exception: " . $e->getMessage());
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $user->setImgUrl($newFilename);
        }


        try {
            $em = $doct->getManager();
            $em->flush();

            return new JsonResponse("Image added successfully", 200);
        } catch (\Exception $ex) {
            return new Response("exception: " . $ex->getMessage());
        }


        // http://127.0.0.1:8000/user/api/updateProfile?fullname=foulen%20benfoulen&email=foulen@gmail.com&tel=25147963&description=teststest&fbLink=tests&instaLink=tests&twitterLink=tests&ProfilePic=
    }
}
