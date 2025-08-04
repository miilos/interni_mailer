<?php

namespace App\Service\Search;

use App\Dto\SearchCriteria\UserSearchCriteria;
use App\Repository\UserRepository;

class UserSearchService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function searchByCriteria(UserSearchCriteria $criteria): array
    {
        $qb = $this->userRepository->buildSearch($criteria);
        return $qb->getQuery()->getResult();
    }
}
