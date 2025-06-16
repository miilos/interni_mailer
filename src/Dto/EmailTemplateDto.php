<?php

namespace App\Dto;

use App\Validator\UniqueTemplateName\UniqueTemplateName;
use Symfony\Component\Validator\Constraints as Assert;

class EmailTemplateDto
{
    public function __construct(
        // the validator runs only if the template name is user-generated
        #[UniqueTemplateName]
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

        #[Assert\NotBlank(message: 'Template body is required!')]
        private ?string $body = null,

        private ?string $twigTemplateName = null
    ) {}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getFrom(): ?string
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

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function setFrom(?string $from): void
    {
        $this->from = $from;
    }

    public function setTo(array $to): void
    {
        $this->to = $to;
    }

    public function setCc(array $cc): void
    {
        $this->cc = $cc;
    }

    public function setBcc(array $bcc): void
    {
        $this->bcc = $bcc;
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    public function getTwigTemplateName(): ?string
    {
        return $this->twigTemplateName;
    }

    public function setTwigTemplateName(?string $twigTemplateName): void
    {
        $this->twigTemplateName = $twigTemplateName;
    }
}
