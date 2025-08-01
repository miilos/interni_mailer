<?php

namespace App\Service\Search;

use App\Dto\SearchCriteria\EmailTemplateSearchCriteria;
use App\Repository\EmailTemplateRepository;

class EmailTemplateSearchService
{
    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
    ) {}

    public function searchByCriteria(EmailTemplateSearchCriteria $criteria): array
    {
        $qb = $this->emailTemplateRepository->buildSearch($criteria);
        return $qb->getQuery()->getResult();
    }
}
