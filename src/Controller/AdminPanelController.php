<?php

namespace App\Controller;

use App\Repository\EmailLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPanelController extends AbstractController
{
    #[Route('/admin', name: 'admin_panel')]
    public function adminPanel(): Response
    {
        return $this->render('admin/admin_panel.html.twig');
    }
}
