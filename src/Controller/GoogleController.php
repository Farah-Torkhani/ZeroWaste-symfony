<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google_start')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // on Symfony 3.3 or lower, $clientRegistry = $this->get('knpu.oauth2.registry');

        // will redirect to Facebook!
        return $clientRegistry
            ->getClient('google') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect();
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry, ManagerRegistry $doct)
    {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)

        $client = $clientRegistry->getClient('google');
        // dd($client->fetchUser()->getEmail());
        $user = $doct->getRepository(User::class)->findOneBy(['email' => $client->fetchUser()->getEmail()]);

        if (!$user) {
            $this->addFlash('warning', 'email not associated with zeroWaste account');
            return $this->redirectToRoute("app_login");
        } else {
            return $this->redirectToRoute("app_dash_user_home");
        }

        // try {

        //     $user = $client->fetchUser();

        //     // do something with all this new power!
        //     // e.g. $name = $user->getFirstName();
        //     var_dump($user);
        //     die;
        //     // ...
        // } catch (\Exception $e) {
        //     // something went wrong!
        //     // probably you should return the reason to the user
        //     var_dump($e->getMessage());
        //     die;
        // }
    }
}
