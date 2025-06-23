<?php

namespace App\Service;

use App\Dto\BodyContent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailBuilderService
{
    private Email $email;
    private array $sendTo = [];

    public function __construct(
        private EmailParserService $emailParser,
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
        $this->email->subject(
            $this->emailParser->parseVariables($subject)
        );

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

    public function body(BodyContent $body): static
    {
        $bodyContent = "";

        if ($body->usesTemplate()) {
            $bodyContent = $this->emailParser->parseBodyTemplate($body->getContent());
        }
        else {
            $bodyContent = $body->getContent();
        }

        $bodyContent = $this->emailParser->parseVariables($bodyContent);

        if ($this->emailParser->isHtml($bodyContent)) {
            $this->email->html($bodyContent);
        }
        else {
            $this->email->text($bodyContent);
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
