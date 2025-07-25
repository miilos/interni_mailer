<?php

namespace App\Controller;

use App\Dto\SearchCriteria\LogSearchCriteria;
use App\Entity\EmailStatusEnum;
use App\Service\Search\LogSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

class LogController extends AbstractController
{
    #[Route('/api/logs', name: 'getLogs')]
    public function getLogs(
        LogSearchService $logSearchService,
        #[MapQueryString]
        LogSearchCriteria $criteria
    ): JsonResponse
    {
        $paginator = $logSearchService->searchByCriteria($criteria);
        $logs = $paginator->getItems();

        return $this->json([
            'status' => 'success',
            'results' => count($logs),
            'data' => [
                'logs' => $logs,
            ],
            'pagination' => [
                'currentPage' => $paginator->getCurrentPageNumber(),
                'totalPages' => $paginator->getPageCount(),
                'totalItems' => $paginator->getTotalItemCount(),
                'itemsPerPage' => $paginator->getItemNumberPerPage(),
                'hasNextPage' => $paginator->getCurrentPageNumber() < $paginator->getPageCount(),
                'hasPrevPage' => $paginator->getCurrentPageNumber() > 1,
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
