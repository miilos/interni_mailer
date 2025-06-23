<?php

namespace App\Service;

use App\Dto\BodyContent;
use App\Dto\EmailDto;

class EmailSenderService
{
    public function __construct(
        private EmailBuilderService $builder,
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
            $emailBuilder = $emailBuilder->body(BodyContent::template($emailDto->getBodyTemplate()));
        }
        else {
            $emailBuilder = $emailBuilder->body(BodyContent::plain($emailDto->getBody()));
        }

        $emailBuilder
            ->to($emailDto->getTo())
            ->send();
    }
}
