<?php

namespace App\Controller;

use App\Dto\EmailBodyDto;
use App\Repository\EmailBodyRepository;
use App\Service\EmailBodyFileCreatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class EmailBodyController extends AbstractController
{
    #[Route('/api/email-body', methods: ['POST'])]
    public function createSubject(
        EmailBodyRepository $emailBodyRepository,
        EmailBodyFileCreatorService $emailBodyFileCreatorService,
        #[MapRequestPayload]
        EmailBodyDto $emailBodyDto
    ): JsonResponse
    {
        $body = $emailBodyRepository->createEmailSubject($emailBodyDto);

        $emailBodyFileCreatorService->createBodyTemplateFile($emailBodyDto);

        return $this->json([
            'status' => 'success',
            'message' => 'Email body template created successfully!',
            'data' => [
                'body' => $body,
            ]
        ], 201);
    }
}
