<?php

namespace App\EventSubscriber;

use App\Event\SendEmailEvent;
use App\Service\EmailSenderService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SendEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EmailSenderService $sender
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
    }
}
