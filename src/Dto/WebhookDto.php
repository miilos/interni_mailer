<?php

namespace App\Dto;

class WebhookDto
{
    public function __construct(
        private string $event,
        private string $recipient,
        private string $emailId,
        private ?string $error = null
    ) {}

    public function getEvent(): string
    {
        return $this->event;
    }

    public function setEvent(string $event): void
    {
        $this->event = $event;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function setRecipient(string $recipient): void
    {
        $this->recipient = $recipient;
    }

    public function getEmailId(): string
    {
        return $this->emailId;
    }

    public function setEmailId(string $emailId): void
    {
        $this->emailId = $emailId;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): void
    {
        $this->error = $error;
    }
}
