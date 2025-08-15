<?php

namespace App\Controller;

use App\Dto\EmailTemplateDto;
use App\Dto\SearchCriteria\EmailTemplateSearchCriteria;
use App\Entity\EmailTemplate;
use App\Repository\EmailTemplateRepository;
use App\Service\Search\EmailTemplateSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

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
        ], context: ['groups' => 'basicTemplateData']);
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
        ], 201, context: ['groups' => 'basicTemplateData']);
    }

    #[Route('/api/templates/{id}', methods: ['PATCH'])]
    public function updateTemplate(
        EmailTemplate $template,
        DecoderInterface $decoder,
        Request $request,
    ): JsonResponse
    {
        $valuesToChange = $decoder->decode($request->getContent(), 'json');

        $template = $this->emailTemplateRepository->updateEmailTemplate($template, $valuesToChange);

        return $this->json([
            'status' => 'success',
            'message' => 'Template updated successfully!',
            'data' => [
                'template' => $template
            ]
        ], context: ['groups' => 'basicTemplateData']);
    }

    #[Route('/api/templates/{id}', methods: ['DELETE'])]
    public function deleteTemplate(
        EmailTemplate $template,
    ): JsonResponse
    {
        $this->emailTemplateRepository->deleteTemplate($template);

        return $this->json([], 204);
    }
}
