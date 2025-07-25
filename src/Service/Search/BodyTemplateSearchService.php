<?php

namespace App\Service\Search;

use App\Dto\SearchCriteria\BodyTemplateSearchCriteria;
use App\Repository\EmailBodyRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

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
