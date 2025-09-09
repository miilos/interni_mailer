<?php

namespace App\Controller;

use App\Dto\UserDto;
use App\Form\LoginFormType;
use App\Form\PasskeySetupFormType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPanelController extends AbstractController
{
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

    #[Route('/admin/users/create', name: 'create_user')]
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
            $userRepository->createUser($user);

            $this->addFlash('success', 'User created!');
        }

        return $this->render('security/create_user.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        $loginForm = $this->createForm(LoginFormType::class);
        $passkeySetupForm = $this->createForm(PasskeySetupFormType::class);

        return $this->render('security/login.html.twig', [
            'loginForm' => $loginForm,
            'passkeySetupForm' => $passkeySetupForm
        ]);
    }
}
