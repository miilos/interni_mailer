<?php

namespace App\Dto\SearchCriteria;

abstract class SearchCriteria
{
    protected string $sortBy = 'id';
    protected string $sortDirection = 'ASC';

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function setSortBy(string $sortBy): static
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    public function getSortDirection(): string
    {
        return $this->sortDirection;
    }

    public function setSortDirection(string $sortDirection): static
    {
        $this->sortDirection = $sortDirection;

        return $this;
    }
}
