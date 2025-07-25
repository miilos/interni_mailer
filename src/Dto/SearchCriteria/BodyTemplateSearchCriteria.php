<?php

namespace App\Dto\SearchCriteria;

class BodyTemplateSearchCriteria extends SearchCriteria
{
    private ?string $name = null;
    private ?string $format = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }
}
