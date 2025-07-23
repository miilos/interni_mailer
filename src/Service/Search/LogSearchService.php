<?php

namespace App\Service\Search;

use App\Dto\LogSearchCriteria;
use App\Repository\EmailLogRepository;

class LogSearchService
{
    public function __construct(
        private EmailLogRepository $emailLogRepository
    ) {}

    public function search(LogSearchCriteria $criteria): array
    {
        return $this->emailLogRepository->findByCriteria($criteria);
    }
}
