<?php

namespace App\Service;

use App\Dto\EmailDto;

class EmailSenderService
{
    public function __construct(
        private EmailBuilderService $builder,
    ) {}

    public function send(EmailDto $emailDto): void
    {
        $this->builder
            ->createEmail()
            ->subject($emailDto->getSubject())
            ->from($emailDto->getFrom())
            ->cc($emailDto->getCc())
            ->bcc($emailDto->getBcc())
            ->body($emailDto->getBody())
            ->to($emailDto->getTo())
            ->send();
    }
}
