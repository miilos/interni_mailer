<?php

namespace App\Dto;

class EmailLogDto
{
    private string $emailId;
    private ?string $templateName;
    private string $subject;
    private string $from;
    private array $to;
    private array $cc;
    private array $bcc;
    private string $body;
    private string $status;
    private \DateTimeImmutable $loggedAt;

    public function __construct (
        EmailDto $emailDto,
        string $status
    ) {
        $this->emailId = $emailDto->getId();
        $this->templateName = $emailDto->getBodyTemplate();
        $this->subject = $emailDto->getSubject();
        $this->from = $emailDto->getFrom();
        $this->to = $emailDto->getTo();
        $this->cc = $emailDto->getCc();
        $this->bcc = $emailDto->getBcc();
        $this->body = $emailDto->getBody();
        $this->status = $status;
        $this->loggedAt = new \DateTimeImmutable();
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

    public function getTo(): array
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

    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }
}
