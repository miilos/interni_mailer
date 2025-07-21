<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailBuilderService
{
    private Email $email;

    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function createEmail(bool $usesTemplate = false): static
    {
        if ($usesTemplate) {
            $this->email = new TemplatedEmail();
        }
        else {
            $this->email = new Email();
        }

        return $this;
    }

    public function subject(string $subject): static
    {
        $this->email->subject($subject);

        return $this;
    }

    public function from(string $from): static
    {
        $this->email->from($from);

        return $this;
    }

    public function to(string $to): static
    {
        $this->email->to($to);

        return $this;
    }

    public function cc(array $cc): static
    {
        $this->email->cc(...$cc);

        return $this;
    }

    public function bcc(array $bcc): static
    {
        $this->email->bcc(...$bcc);

        return $this;
    }

    public function body(string $body): static
    {
        $this->email->html($body);

        return $this;
    }

    public function send(): void
    {
        $this->mailer->send($this->email);
    }
}
