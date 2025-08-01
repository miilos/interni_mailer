<?php

namespace App\Dto\SearchCriteria;

class EmailTemplateSearchCriteria extends SearchCriteria
{
    private ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
