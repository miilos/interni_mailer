<?php

namespace App\Controller;

use App\Dto\EmailBodyDto;
use App\Message\CreateBodyTemplateFile;
use App\Repository\EmailBodyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class EmailBodyController extends AbstractController
{
    #[Route('/api/email-body', methods: ['POST'])]
    public function createSubject(
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
