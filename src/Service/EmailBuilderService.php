<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class EmailBuilderService
{
    private string $subject = '';
    private string $from = '';
    private array $to = [];
    private ?array $cc = [];
    private ?array $bcc = [];
    private string $body = '';

    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function subject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function from(string $from): static
    {
        $this->from = $from;

        return $this;
    }

    // doesn't change the email object so that a separate email can be sent for every address passed
    public function to(array $to): static
    {
        $this->to = $to;

        return $this;
    }

    public function cc(array $cc): static
    {
        $this->cc = $cc;

        return $this;
    }

    public function bcc(array $bcc): static
    {
        $this->bcc = $bcc;

        return $this;
    }

    public function body(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function send(): void
    {
        $emailCount = 1;

        foreach ($this->to as $to) {
            $email = (new TemplatedEmail())
                ->subject($this->subject)
                ->from($this->from)
                ->to($to)
                ->html($this->body);

            // set cc and bcc only on the first email if multiple emails are being sent,
            // so that the same person doesn't get cc'd or bcc'd multiple times for the same email
            if ($emailCount === 1) {
                $email->cc(...$this->cc);
                $email->bcc(...$this->bcc);
            }

            $this->mailer->send($email);

            $emailCount++;
        }
    }
}
