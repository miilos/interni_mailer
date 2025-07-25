<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPanelController extends AbstractController
{
    #[Route('/admin/logs', name: 'admin_panel')]
    public function adminPanel(): Response
    {
        return $this->render('admin/admin_panel.html.twig');
    }

    #[Route('/admin/templates-body', name: 'body_template_overview')]
    public function templateOverview(): Response
    {
        return $this->render('admin/body_template_overview.html.twig');
    }
}
