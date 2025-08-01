<?php

namespace App\Entity;

use App\Dto\EmailDto;
use App\Repository\EmailTemplateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[ORM\Entity(repositoryClass: EmailTemplateRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'That template name is already in use!')]
#[Map(target: EmailDto::class)]
class EmailTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(length: 255)]
    #[Map(target: 'from')]
    private ?string $fromAddr = null;

    #[ORM\Column(type: Types::JSON)]
    #[Map(target: 'to')]
    private array $toAddr = [];

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $cc = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $bcc = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bodyTemplateName = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'body_template')]
    private ?EmailBody $bodyTemplate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getCc(): ?array
    {
        return $this->cc;
    }

    public function setCc(?array $cc): static
    {
        $this->cc = $cc;

        return $this;
    }

    public function getBcc(): ?array
    {
        return $this->bcc;
    }

    public function setBcc(?array $bcc): static
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getBodyTemplateName(): ?string
    {
        return $this->bodyTemplateName;
    }

    public function setBodyTemplateName(?string $bodyTemplateName): static
    {
        $this->bodyTemplateName = $bodyTemplateName;

        return $this;
    }

    public function getBodyTemplate(): ?EmailBody
    {
        return $this->bodyTemplate;
    }

    public function setBodyTemplate(?EmailBody $bodyTemplate): static
    {
        $this->bodyTemplate = $bodyTemplate;

        return $this;
    }
}
