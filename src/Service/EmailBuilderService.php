<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailBuilderService
{
    private Email $email;
    private array $sendTo = [];

    public function __construct(
        private EmailBodyTemplateParserService $emailBodyTemplateParser,
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

    // doesn't change the email object so that a separate email can be sent for every address passed
    public function to(array $to): static
    {
        $this->sendTo = $to;

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

    public function plainBody(string $body): static
    {
        $this->email->text($body);

        return $this;
    }

    public function bodyTemplate(string $templateName): static
    {
        $body = $this->emailBodyTemplateParser->parseBodyTemplate($templateName);

        if (preg_match('/<\s?[^\>]*\/?\s?>/i', $body)) {
            $this->email->html($body);
        }
        else {
            $this->email->text($body);
        }

        return $this;
    }

    public function send(): void
    {
        foreach ($this->sendTo as $to) {
            $this->email->to($to);
            $this->mailer->send($this->email);
        }
    }
}
