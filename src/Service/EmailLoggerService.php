<?php

namespace App\Service;

use App\Dto\EmailDto;
use App\Entity\EmailStatusEnum;
use App\Message\LogEmail;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailLoggerService
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    public function logSent(EmailDto $emailDto): void
    {
        // $emailDto has an array of all the addresses the email is going to,
        // so the dto has to be cloned in order to set only one address for logging
        foreach ($emailDto->getTo() as $to) {
            $this->messageBus->dispatch(new LogEmail($emailDto, $to, EmailStatusEnum::SENT->value));
        }
    }

    // when a batch fails to send, also log the error for each email so that errors can be viewed on the frontend
    public function logBatchSendingError(EmailDto $emailDto, string $error): void
    {
        foreach ($emailDto->getTo() as $to) {
            $this->messageBus->dispatch(new LogEmail($emailDto, $to, EmailStatusEnum::FAILED->value, $error));
        }
    }
}
