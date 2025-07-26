<?php

namespace App\Service\Search;

use App\Dto\SearchCriteria\BodyTemplateSearchCriteria;
use App\Repository\EmailBodyRepository;

class BodyTemplateSearchService
{
    public function __construct(
        private EmailBodyRepository $emailBodyRepository
    ) {}

    public function searchByCriteria(BodyTemplateSearchCriteria $criteria): array
    {
        $qb = $this->emailBodyRepository->buildSearch($criteria);
        return $qb->getQuery()->getResult();
    }
}
