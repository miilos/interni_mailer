<?php

namespace App\Controller;

use App\Dto\EmailTemplateDto;
use App\Repository\EmailTemplateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class EmailTemplateController extends AbstractController
{
    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
    ) {}

    #[Route('/api/templates', methods: ['POST'])]
    public function createTemplate(#[MapRequestPayload] EmailTemplateDto $emailTemplateDto): JsonResponse
    {
        $template = $this->emailTemplateRepository->createEmailTemplate($emailTemplateDto);

        return $this->json([
            'status' => 'success',
            'message' => 'Template created successfully!',
            'data' => [
                'template' => $template
            ]
        ], 201);
    }
}
