<?php

namespace App\Message;

use App\Entity\EmailBody;

class UpdateBodyTemplateChangelog
{
    public function __construct(
        private int $bodyTemplateId,
        private array $diff
    ) {}

    public function getBodyTemplateId(): int
    {
        return $this->bodyTemplateId;
    }

    public function getDiff(): array
    {
        return $this->diff;
    }
}
