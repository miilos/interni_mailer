<?php

namespace App\MessageHandler;

use App\Entity\EmailStatusEnum;
use App\Message\LogEmail;
use App\Message\SendEmail;
use App\Service\EmailSenderService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class SendEmailHandler
{
    public function __construct(
        private EmailSenderService $sender,
        private MessageBusInterface $messageBus,
    ) {}

    public function __invoke(SendEmail $sendEmail)
    {
        $this->sender->send($sendEmail->getEmail());
        $this->messageBus->dispatch(new LogEmail($sendEmail->getEmail(), EmailStatusEnum::SENT->value));
    }
}
