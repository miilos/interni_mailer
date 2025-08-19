<?php

namespace App\Controller;

use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class StatisticsController extends AbstractController
{
    #[Route('/api/stats', methods: ['GET'])]
    public function getStats(
        StatisticsService $statisticsService,
        Request $request
    ): JsonResponse
    {
        $period = $request->query->get('period') ?? 'month';

        $statistics = $statisticsService->getStatistics($period);

        return $this->json([
            'status' => 'success',
            'data' => [
                'statistics' => $statistics
            ]
        ]);
    }
}
