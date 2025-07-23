<?php

namespace App\Controller;

use App\Dto\LogSearchCriteria;
use App\Entity\EmailStatusEnum;
use App\Repository\EmailLogRepository;
use App\Service\Search\LogSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        LogSearchService $logSearchService,
        Request $request,
        DecoderInterface $decoder
    ): JsonResponse
    {
        $reqSearchCriteria = $decoder->decode($request->getContent(), 'json');

        $criteria = (new LogSearchCriteria())
            ->setSubject($reqSearchCriteria['subject'] ?? null)
            ->setFrom($reqSearchCriteria['from'] ?? null)
            ->setTo($reqSearchCriteria['to'] ?? null)
            ->setStatus($reqSearchCriteria['status'] ?? null)
            ->setBodyTemplate($reqSearchCriteria['bodyTemplate'] ?? null)
            ->setEmailTemplate($reqSearchCriteria['emailTemplate'] ?? null);

        $logs = $logSearchService->search($criteria);

        return $this->json([
            'status' => 'success',
            'results' => count($logs),
            'data' => [
                'logs' => $logs,
            ]
        ]);
    }

    // returns all the possible statuses for the email so they can be displayed on the frontend
    #[Route('/api/logs/statuses', name: 'getLogStatuses')]
    public function getEmailStatusValues(): JsonResponse
    {
        $statuses = EmailStatusEnum::values();

        return $this->json([
            'status' => 'success',
            'data' => [
                'statuses' => $statuses,
            ]
        ]);
    }
}
