<?php

namespace App\Entity;

use App\Dto\EmailDto;
use App\Repository\EmailBatchRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[ORM\Entity(repositoryClass: EmailBatchRepository::class)]
#[Map(target: EmailDto::class)]
class EmailBatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $batchId = null;

    #[ORM\Column(length: 255)]
    #[Map(target: 'id')]
    private ?string $emailId = null;

    #[ORM\Column(length: 255)]
    #[Map(target: 'subject')]
    private ?string $subject = null;

    #[ORM\Column(length: 255)]
    #[Map(target: 'from')]
    private ?string $fromAddr = null;

    #[ORM\Column(type: Types::JSON)]
    #[Map(target: 'to')]
    private array $toAddr = [];

    #[ORM\Column(type: Types::JSON)]
    #[Map(target: 'cc')]
    private array $cc = [];

    #[ORM\Column(type: Types::JSON)]
    #[Map(target: 'bcc')]
    private array $bcc = [];

    #[ORM\Column(type: Types::TEXT)]
    #[Map(target: 'body')]
    private ?string $body = null;

    #[ORM\Column(enumType: EmailBatchStatusEnum::class)]
    private ?EmailBatchStatusEnum $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dispatchedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Map(target: 'bodyTemplate')]
    private ?string $bodyTemplate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Map(target: 'emailTemplate')]
    private ?string $emailTemplate = null;

    #[ORM\Column(nullable: true)]
    private ?int $numFailedResends = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $error = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBatchId(): ?string
    {
        return $this->batchId;
    }

    public function setBatchId(string $batchId): static
    {
        $this->batchId = $batchId;

        return $this;
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

    public function getStatus(): ?EmailBatchStatusEnum
    {
        return $this->status;
    }

    public function setStatus(EmailBatchStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDispatchedAt(): ?\DateTimeImmutable
    {
        return $this->dispatchedAt;
    }

    public function setDispatchedAt(\DateTimeImmutable $dispatchedAt): static
    {
        $this->dispatchedAt = $dispatchedAt;

        return $this;
    }

    public function getBodyTemplate(): ?string
    {
        return $this->bodyTemplate;
    }

    public function setBodyTemplate(?string $bodyTemplate): static
    {
        $this->bodyTemplate = $bodyTemplate;

        return $this;
    }

    public function getEmailTemplate(): ?string
    {
        return $this->emailTemplate;
    }

    public function setEmailTemplate(?string $emailTemplate): static
    {
        $this->emailTemplate = $emailTemplate;

        return $this;
    }

    public function getNumFailedResends(): ?int
    {
        return $this->numFailedResends;
    }

    public function setNumFailedResends(?int $numFailedResends): static
    {
        $this->numFailedResends = $numFailedResends;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): static
    {
        $this->error = $error;

        return $this;
    }
}
