<?php

namespace App\Dto\SearchCriteria;

class AddressSearchCriteria
{
    public function __construct(
        private ?string $email = null,
    ) {}

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
