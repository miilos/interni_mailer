<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPanelController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        return $this->render('login.html.twig');
    }

    #[Route('/', name: 'send_email')]
    public function sendEmail(): Response
    {
        return $this->render('admin/send_email.html.twig');
    }

    #[Route('/admin/logs', name: 'admin_panel')]
    public function adminPanel(): Response
    {
        return $this->render('admin/logs.html.twig');
    }

    #[Route('/admin/templates-body', name: 'body_template_overview')]
    public function bodyTemplateOverview(): Response
    {
        return $this->render('admin/body_template_overview.html.twig');
    }

    #[Route('/admin/templates-body/add', name: 'add_body_template')]
    public function addBodyTemplate(): Response
    {
        return $this->render('admin/add_body_template.html.twig');
    }

    #[Route('/admin/templates-email', name: 'email_template_overview')]
    public function emailTemplateOverview(): Response
    {
        return $this->render('admin/email_template_overview.html.twig');
    }

    #[Route('/admin/groups', name: 'groups')]
    public function groups(): Response
    {
        return $this->render('admin/groups.html.twig');
    }

    #[Route('/admin/statistics', name: 'statistics')]
    public function statistics(): Response
    {
        return $this->render('admin/statistics.html.twig');
    }
}
