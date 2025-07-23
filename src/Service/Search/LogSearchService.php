<?php

namespace App\Service\Search;

use App\Dto\LogSearchCriteria;
use App\Repository\EmailLogRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class LogSearchService
{
    public function __construct(
        private EmailLogRepository $emailLogRepository,
        private PaginatorInterface $paginator,
    ) {}

    public function searchAll(LogSearchCriteria $criteria): PaginationInterface
    {
        $qb = $this->emailLogRepository->buildFindAll();

        return $this->paginator->paginate(
            $qb,
            $criteria->getPage(),
            $criteria->getLimit()
        );
    }

    public function searchByCriteria(LogSearchCriteria $criteria): PaginationInterface
    {
        $qb = $this->emailLogRepository->buildSearch($criteria);

        return $this->paginator->paginate(
            $qb,
            $criteria->getPage(),
            $criteria->getLimit(),
        );
    }
}
