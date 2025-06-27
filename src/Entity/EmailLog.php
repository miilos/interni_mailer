<?php

namespace App\Entity;

use App\Repository\EmailLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailLogRepository::class)]
class EmailLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $emailId = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(length: 255)]
    private ?string $fromAddr = null;

    #[ORM\Column(type: Types::JSON)]
    private array $toAddr = [];

    #[ORM\Column(type: Types::JSON)]
    private array $cc = [];

    #[ORM\Column(type: Types::JSON)]
    private array $bcc = [];

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    #[ORM\Column(enumType: EmailStatusEnum::class)]
    private ?EmailStatusEnum $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $loggedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailId(): ?string
    {
        return $this->emailId;
    }

    public function setEmailId(string $emailId): static
    {
        $this->emailId = $emailId;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getFromAddr(): ?string
    {
        return $this->fromAddr;
    }

    public function setFromAddr(string $fromAddr): static
    {
        $this->fromAddr = $fromAddr;

        return $this;
    }

    public function getToAddr(): array
    {
        return $this->toAddr;
    }

    public function setToAddr(array $toAddr): static
    {
        $this->toAddr = $toAddr;

        return $this;
    }

    public function getCc(): array
    {
        return $this->cc;
    }

    public function setCc(array $cc): static
    {
        $this->cc = $cc;

        return $this;
    }

    public function getBcc(): array
    {
        return $this->bcc;
    }

    public function setBcc(array $bcc): static
    {
        $this->bcc = $bcc;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getStatus(): ?EmailStatusEnum
    {
        return $this->status;
    }

    public function setStatus(EmailStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getLoggedAt(): ?\DateTimeImmutable
    {
        return $this->loggedAt;
    }

    public function setLoggedAt(\DateTimeImmutable $loggedAt): static
    {
        $this->loggedAt = $loggedAt;

        return $this;
    }
}
