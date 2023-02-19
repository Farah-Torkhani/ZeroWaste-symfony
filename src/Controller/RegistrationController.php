<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function registre(ManagerRegistry $doct, Request $request, MailerInterface $mailer, TokenGeneratorInterface  $tokenGenerator, UserPasswordHasherInterface $passwordHasher): Response
    {
        $userr = $this->getUser();
        if ($userr) {
            if (in_array('ROLE_ADMIN', $userr->getRoles(), true)) {
                return $this->redirectToRoute('app_dash_admin_users');
            } else {
                return $this->redirectToRoute('app_dash_user_home');
            }
        }

        $user = new User();
        $registreForm = $this->createForm(UserType::class, $user);
        $registreForm->handleRequest($request);

        if ($registreForm->isSubmitted() && $registreForm->isValid()) {
            $role = $request->get('role', 0)[0];
            if ($role != "ROLE_ASSOCIATION" && $role != "ROLE_USER") {
                $this->addFlash('warning', 'Hello little hacker 🐱‍💻👩‍💻');
                return $this->redirectToRoute('app_registration');
            }
            $user->setRoles([$role]);
            $user->setImgUrl("defaultPic.jpg");
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $token = $tokenGenerator->generateToken();
            $user->setToken($token);



            try {
                // dd($user);
                $em = $doct->getManager();
                $em->persist($user);
                $em->flush();

                //send mail
                $url = $this->generateUrl('app_emailVerif', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
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
                $this->addFlash('success', 'Please confirm your email');
                return $this->redirectToRoute("app_login");
                // return $this->redirectToRoute("app_registration");
            } catch (\Exception $exception) {
                $this->addFlash('warning', 'une erreur est survenue : email already in use');
                return $this->redirectToRoute("app_registration");
            }
        }

        return $this->render('registration/index.html.twig', [
            'title' => 'Zero Waste',
            'registreForm' => $registreForm->createView(),
        ]);
    }






    #[Route('/emailVerif/{token}', name: 'app_emailVerif')]
    public function emailVerif(ManagerRegistry $doct, TokenGeneratorInterface  $tokenGenerator, $token): Response
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
            $this->addFlash('warning', 'error');
            // return $this->redirectToRoute("app_login");
            return $this->redirectToRoute("app_registration");
        }

        $user->setIsVerified(true);
        $token = $tokenGenerator->generateToken();
        $user->setToken($token);
        $em = $doct->getManager();
        $em->flush();

        $this->addFlash('success', 'email verified successfully');
        return $this->redirectToRoute("app_login");
    }
}
