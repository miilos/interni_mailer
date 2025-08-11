<?php

namespace App\Message;

class SendEmail
{
    public function __construct(private string $batchId) {}

    public function getBatchId(): string
    {
        return $this->batchId;
    }
}
