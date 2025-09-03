<?php

namespace App\Controller;

use App\Dto\UserDto;
use App\Form\PasskeySetupFormType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPanelController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    #[Route('/', name: 'send_email')]
    public function sendEmail(): Response
    {
        return $this->render('admin/send_email.html.twig');
    }

    #[Route('/logs', name: 'admin_panel')]
    public function adminPanel(): Response
    {
        return $this->render('admin/logs.html.twig');
    }

    #[Route('/templates-body', name: 'body_template_overview')]
    public function bodyTemplateOverview(): Response
    {
        return $this->render('admin/body_template_overview.html.twig');
    }

    #[Route('/templates-body/add', name: 'add_body_template')]
    public function addBodyTemplate(): Response
    {
        return $this->render('admin/add_body_template.html.twig');
    }

    #[Route('/templates-email', name: 'email_template_overview')]
    public function emailTemplateOverview(): Response
    {
        return $this->render('admin/email_template_overview.html.twig');
    }

    #[Route('/groups', name: 'groups')]
    public function groups(): Response
    {
        return $this->render('admin/groups.html.twig');
    }

    #[Route('/statistics', name: 'statistics')]
    public function statistics(): Response
    {
        return $this->render('admin/statistics.html.twig');
    }

    #[Route('/users/create', name: 'create_user')]
    public function createUser(
        Request $request,
        UserRepository $userRepository,
    ): Response
    {
        $newUser = new UserDto();

        $form = $this->createForm(RegistrationFormType::class, $newUser);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $persistedUser = $userRepository->createUser($user);
            $request->getSession()->set('new_user_id', $persistedUser->getId());

            $this->addFlash('success', 'User created!');
        }

        return $this->render('security/create_user.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/users/create/passkey', name: 'create_passkey')]
    public function setUpPasskey(
        Request $request,
        UserRepository $userRepository
    ): Response
    {
        $session = $request->getSession();
        $id = $session->get('new_user_id');

        $user = $userRepository->findOneBy(['id' => $id]);

        $form = $this->createForm(PasskeySetupFormType::class, [
            'email' => $user->getEmail(),
        ]);

        return $this->render('security/passkey_setup.html.twig', [
            'form' => $form,
            'email' => $user->getEmail(),
        ]);
    }
}
