<?php

namespace App\Dto;

use App\Validator\EmailBodyNotNull\EmailBodyNotNull;
use Symfony\Component\Validator\Constraints as Assert;

#[EmailBodyNotNull]
class EmailDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Subject can\'t blank!')]
        private ?string $subject = null,

        #[Assert\NotBlank(message: 'Your email address can\'t blank!')]
        #[Assert\Email(message: 'This is not a valid email address!')]
        private ?string $from = null,

        #[Assert\NotBlank(message: 'Receiver address(es) can\'t blank!')]
        private array $to = [],

        private array $cc = [],

        private array $bcc = [],

        private ?string $body = null,

        private ?string $bodyTemplate = null
    ) {}

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

    public function getBodyTemplate(): ?string
    {
        return $this->bodyTemplate;
    }

    public function setBodyTemplate(?string $bodyTemplate): void
    {
        $this->bodyTemplate = $bodyTemplate;
    }
}
