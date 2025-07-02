<?php

namespace App\EventListener;

use App\Entity\EmailStatusEnum;
use App\Message\LogEmail;
use App\Message\SendEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEventListener]
final class MailerWorkerFailureListener
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    public function __invoke(WorkerMessageFailedEvent $event): void
    {
        $message = $event->getEnvelope()->getMessage();

        if (!$message instanceof SendEmail) {
            return;
        }

        $email = $message->getEmail();
        $error = $event->getThrowable()->getMessage();
        $this->messageBus->dispatch(new LogEmail($email, EmailStatusEnum::FAILED->value, $error));
    }
}
