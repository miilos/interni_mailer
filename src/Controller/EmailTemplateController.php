<?php

namespace App\Controller;

use App\Dto\EmailTemplateDto;
use App\Dto\SearchCriteria\EmailTemplateSearchCriteria;
use App\Repository\EmailTemplateRepository;
use App\Service\Search\EmailTemplateSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class EmailTemplateController extends AbstractController
{
    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
    ) {}

    #[Route('/api/templates', methods: ['GET'])]
    public function getAllTemplates(
        EmailTemplateSearchService $emailTemplateSearchService,
        #[MapQueryString]
        EmailTemplateSearchCriteria $criteria
    ): JsonResponse
    {
        $templates = $emailTemplateSearchService->searchByCriteria($criteria);

        return $this->json([
            'status' => 'success',
            'results' => count($templates),
            'data' => [
                'templates' => $templates
            ]
        ]);
    }

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
