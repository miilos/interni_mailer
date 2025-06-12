<?php

namespace App\Service;

use App\Dto\EmailDto;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function send(EmailDto $emailDto): void
    {
        $email = (new Email())
            ->from($emailDto->getFrom())
            ->subject($emailDto->getSubject())
            ->text($emailDto->getBody());

        foreach ($emailDto->getTo() as $to) {
            $email->addTo($to);
        }

        if ($emailDto->getCc()) {
            foreach ($emailDto->getCc() as $cc) {
                $email->addCc($cc);
            }
        }

        if ($emailDto->getBcc()) {
            foreach ($emailDto->getBcc() as $bcc) {
                $email->addBcc($bcc);
            }
        }

        $this->mailer->send($email);
    }
}
