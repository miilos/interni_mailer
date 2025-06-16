<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

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

        #[Assert\NotBlank(message: 'Email body can\'t blank!')]
        private ?string $body = null,

        private ?string $twigTemplate = null
    ) {}

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

    public function getTwigTemplate(): ?string
    {
        return $this->twigTemplate;
    }

    public function setTwigTemplate(?string $twigTemplate): void
    {
        $this->twigTemplate = $twigTemplate;
    }
}
