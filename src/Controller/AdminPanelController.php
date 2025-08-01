<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPanelController extends AbstractController
{
    #[Route('/', name: 'send_email')]
    public function sendEmail(): Response
    {
        return $this->render('admin/send_email.html.twig');
    }

    #[Route('/admin/logs', name: 'admin_panel')]
    public function adminPanel(): Response
    {
        return $this->render('admin/admin_panel.html.twig');
    }

    #[Route('/admin/templates-body', name: 'body_template_overview')]
    public function bodyTemplateOverview(): Response
    {
        return $this->render('admin/body_template_overview.html.twig');
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
}
