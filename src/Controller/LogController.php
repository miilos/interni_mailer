<?php

namespace App\Controller;

use App\Repository\EmailLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class LogController extends AbstractController
{
    #[Route('/api/logs', name: 'getLogs')]
    public function getAllLogs(
        EmailLogRepository $emailLogRepository,
    ): JsonResponse
    {
        $logs = $emailLogRepository->findAll();

        return $this->json([
            'status' => 'success',
            'data' => [
                'logs' => $logs,
            ],
        ]);
    }

    #[Route('/api/logs/search', methods: ['POST'])]
    public function searchLogs(
        EmailLogRepository $emailLogRepository,
        DecoderInterface $decoder
    ): JsonResponse
    {

    }
}
