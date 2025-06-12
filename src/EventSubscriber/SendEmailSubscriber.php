<?php

namespace App\EventSubscriber;

use App\Event\SendEmailEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SendEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailerService $mailer
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SendEmailEvent::class => 'onSendEmail',
        ];
    }

    public function onSendEmail(SendEmailEvent $event): void
    {
        $this->mailer->send($event->getEmail());
    }
}
