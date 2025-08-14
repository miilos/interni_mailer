<?php

namespace App\Dto;

class EmailLogDto
{
    private string $emailId;
    private ?string $bodyTemplateName;
    private ?string $emailTemplate;
    private string $subject;
    private string $from;
    private string $to;
    private array $cc;
    private array $bcc;
    private string $body;
    private string $status;
    private \DateTimeImmutable $loggedAt;
    private ?string $error;

    public function __construct (
        EmailDto $emailDto,
        string $to,
        string $status,
        ?string $error = null,
    ) {
        $this->emailId = $emailDto->getId();
        $this->bodyTemplateName = $emailDto->getBodyTemplate();
        $this->emailTemplate = $emailDto->getEmailTemplate();
        $this->subject = $emailDto->getSubject();
        $this->from = $emailDto->getFrom();
        $this->to = $to;
        $this->cc = $emailDto->getCc();
        $this->bcc = $emailDto->getBcc();
        $this->body = $emailDto->getBody();
        $this->status = $status;
        $this->loggedAt = new \DateTimeImmutable();
        $this->error = $error;
    }

    public function getEmailId(): string
    {
        return $this->emailId;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getCc(): array
    {
        return $this->cc;
    }

    public function getBcc(): array
    {
        return $this->bcc;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getLoggedAt(): \DateTimeImmutable
    {
        return $this->loggedAt;
    }

    public function getBodyTemplateName(): ?string
    {
        return $this->bodyTemplateName;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getEmailTemplate(): ?string
    {
        return $this->emailTemplate;
    }
}
