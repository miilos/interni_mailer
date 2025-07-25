<?php

namespace App\Controller;

use App\Dto\EmailBodyDto;
use App\Dto\SearchCriteria\BodyTemplateSearchCriteria;
use App\Message\CreateBodyTemplateFile;
use App\Repository\EmailBodyRepository;
use App\Service\Search\BodyTemplateSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class EmailBodyController extends AbstractController
{
    #[Route('/api/email-body', methods: ['GET'])]
    public function getBodyTemplates(
        BodyTemplateSearchService $bodyTemplateSearchService,
        #[MapQueryString]
        BodyTemplateSearchCriteria $criteria
    ): JsonResponse
    {
        $templates = $bodyTemplateSearchService->searchByCriteria($criteria);

        return $this->json([
            'status' => 'success',
            'results' => count($templates),
            'data' => [
                'templates' => $templates,
            ]
        ]);
    }

    #[Route('/api/email-body', methods: ['POST'])]
    public function createBodyTemplate(
        EmailBodyRepository $emailBodyRepository,
        MessageBusInterface $messageBus,
        #[MapRequestPayload]
        EmailBodyDto $emailBodyDto
    ): JsonResponse
    {
        $body = $emailBodyRepository->createEmailBody($emailBodyDto);

        $messageBus->dispatch(new CreateBodyTemplateFile($emailBodyDto));

        return $this->json([
            'status' => 'success',
            'message' => 'Email body template created successfully!',
            'data' => [
                'body' => $body,
            ]
        ], 201);
    }
}
