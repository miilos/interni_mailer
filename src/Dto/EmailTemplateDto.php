<?php

namespace App\Dto;

use App\Entity\EmailTemplate;
use App\Validator\EmailBodyNotNull\EmailBodyNotNull;
use App\Validator\UniqueName\UniqueName;
use Symfony\Component\Validator\Constraints as Assert;

#[EmailBodyNotNull]
class EmailTemplateDto
{
    public function __construct(
        #[UniqueName(
            entityClass: EmailTemplate::class,
            repoMethod: 'getAllEmailTemplateNames'
        )]
        private ?string $name = null,

        #[Assert\NotBlank(message: 'Template subject is required!')]
        private ?string $subject = null,

        #[Assert\NotBlank(message: 'Template from address(es) is required!')]
        #[Assert\Email(message: 'This is not a valid email address!')]
        private ?string $from = null,

        #[Assert\NotBlank(message: 'Template to address(es) is required!')]
        private array $to = [],

        private array $cc = [],

        private array $bcc = [],

        private ?string $body = null,

        private ?string $bodyTemplateName = null,
    ) {}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setFrom(?string $from): void
    {
        $this->from = $from;
    }

    public function getTo(): array
    {
        return $this->to;
    }

    public function setTo(array $to): void
    {
        $this->to = $to;
    }

    public function getCc(): array
    {
        return $this->cc;
    }

    public function setCc(array $cc): void
    {
        $this->cc = $cc;
    }

    public function getBcc(): array
    {
        return $this->bcc;
    }

    public function setBcc(array $bcc): void
    {
        $this->bcc = $bcc;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    public function getBodyTemplateName(): ?string
    {
        return $this->bodyTemplateName;
    }

    public function setBodyTemplateName(?string $bodyTemplateName): void
    {
        $this->bodyTemplateName = $bodyTemplateName;
    }
}
