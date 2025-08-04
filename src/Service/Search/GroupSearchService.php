<?php

namespace App\Service\Search;

use App\Dto\SearchCriteria\GroupSearchCriteria;
use App\Repository\GroupRepository;

class GroupSearchService
{
    public function __construct(
        private GroupRepository $groupRepository,
    ) {}

    public function searchByCriteria(GroupSearchCriteria $criteria): array
    {
        $qb = $this->groupRepository->buildSearch($criteria);
        return $qb->getQuery()->getResult();
    }
}
