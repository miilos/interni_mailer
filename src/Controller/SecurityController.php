<?php

namespace App\Controller;

use App\Notifier\LoginLinkEmailNotification;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class SecurityController extends AbstractController
{
    // because of the check_post_only config in security.yaml, which is there
    // to make the login link only valid the first time it's used,
    // when the user navigates to this route from the email, just the form will be rendered
    // because he's accessing it with a GET request, and when he POSTs the form data, he will be logged in
    #[Route('/login_check', name: 'login_check')]
    public function check(Request $request): Response
    {
        $expires = $request->query->get('expires');
        $username = $request->query->get('user');
        $hash = $request->query->get('hash');

        return $this->render('security/process_login_link.html.twig', [
            'expires' => $expires,
            'user' => $username,
            'hash' => $hash,
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(
        LoginLinkHandlerInterface $loginLinkHandler,
        UserRepository $userRepository,
        Request $request,
        DecoderInterface $decoder,
        NotifierInterface $notifier,
    ): Response
    {
        if ($request->isMethod('POST')) {
            $email = $decoder->decode($request->getContent(), 'json')['email'];
            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                throw new NotFoundHttpException('No user with that email found!');
            }

            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            $notification = new LoginLinkEmailNotification(
                $loginLinkDetails->getUrl(),
                $loginLinkDetails,
                'Your login link (valid for 5 minutes)',
            );
            $recipient = new Recipient($user->getEmail());
            $notifier->send($notification, $recipient);

            return $this->json([
                'status' => 'success',
                'message' => 'Login link sent!'
            ]);
        }

        return $this->render('security/login.html.twig');
    }
}
