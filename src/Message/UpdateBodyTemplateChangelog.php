<?php

namespace App\Message;

use App\Entity\User;

class UpdateBodyTemplateChangelog
{
    public function __construct(
        private int $bodyTemplateId,
        private array $diff,
        private ?User $user,
    ) {}

    public function getBodyTemplateId(): int
    {
        return $this->bodyTemplateId;
    }

    public function getDiff(): array
    {
        return $this->diff;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
