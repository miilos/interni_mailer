<?php

namespace App\EventSubscriber;

use App\Entity\EmailStatusEnum;
use App\Event\SendEmailEvent;
use App\Message\LogEmail;
use App\Service\EmailSenderService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SendEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EmailSenderService $sender,
        private MessageBusInterface $messageBus,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SendEmailEvent::class => 'onSendEmail',
        ];
    }

    public function onSendEmail(SendEmailEvent $event): void
    {
        $this->sender->send($event->getEmail());
        $this->messageBus->dispatch(new LogEmail($event->getEmail(), EmailStatusEnum::SENT->value));
    }
}
