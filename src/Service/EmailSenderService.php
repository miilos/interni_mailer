<?php

namespace App\Service;

use App\Dto\EmailDto;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailSenderService
{
    public function __construct(
        private EmailBuilderService $builder
    ) {}

    public function send(EmailDto $emailDto): void
    {
        $emailBuilder = $this->builder
            ->createEmail()
            ->subject($emailDto->getSubject())
            ->from($emailDto->getFrom())
            ->cc($emailDto->getCc())
            ->bcc($emailDto->getBcc());

        if ($emailDto->getBodyTemplate()) {
            $emailBuilder = $emailBuilder->bodyTemplate($emailDto->getBodyTemplate());
        }
        else {
            $emailBuilder = $emailBuilder->plainBody($emailDto->getBody());
        }

        $emailBuilder
            ->to($emailDto->getTo())
            ->send();
    }
}
