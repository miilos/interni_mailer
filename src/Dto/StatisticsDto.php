<?php

namespace App\Dto;

class StatisticsDto
{
    public function __construct(
        private int $totalEmails,
        private array $numEmailsByStatus,
        private array $mostUsedEmailTemplates,
        private array $mostUsedBodyTemplates,
    ) {}

    public function getTotalEmails(): int
    {
        return $this->totalEmails;
    }

    public function getNumEmailsByStatus(): array
    {
        return $this->numEmailsByStatus;
    }

    public function getMostUsedEmailTemplates(): array
    {
        return $this->mostUsedEmailTemplates;
    }

    public function getMostUsedBodyTemplates(): array
    {
        return $this->mostUsedBodyTemplates;
    }
}
