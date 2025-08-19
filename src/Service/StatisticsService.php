<?php

namespace App\Service;

use App\Dto\StatisticsDto;
use App\Repository\EmailLogRepository;

class StatisticsService
{
    public function __construct(
        private EmailLogRepository $emailLogRepository,
    ) {}

    public function getStatistics(string $period): StatisticsDto
    {
        $total = $this->emailLogRepository->getTotalEmails($period);
        $statusCounts = $this->emailLogRepository->calculateNumEmailsByStatus($period);
        $statusCounts = $this->addPercentagesToStatusGroups($total, $statusCounts);
        $mostUsedEmailTemplates = $this->emailLogRepository->getMostUsedEmailTemplates($period);
        $mostUsedBodyTemplates = $this->emailLogRepository->getMostUsedBodyTemplates($period);

        return new StatisticsDto(
            totalEmails: $total,
            numEmailsByStatus: $statusCounts,
            mostUsedEmailTemplates: $mostUsedEmailTemplates,
            mostUsedBodyTemplates: $mostUsedBodyTemplates,
        );
    }

    private function addPercentagesToStatusGroups(int $total, array $statusCounts): array
    {
        return array_map(function ($statusCount) use ($total) {
            $statusCount['percentage'] = round(($statusCount['count'] / $total) * 100, 2);
            return $statusCount;
        }, $statusCounts);
    }
}
