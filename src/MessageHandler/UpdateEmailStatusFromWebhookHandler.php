<?php

namespace App\MessageHandler;

use App\Message\UpdateEmailStatusFromWebhook;
use App\Repository\EmailLogRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateEmailStatusFromWebhookHandler
{
    public function __construct(
        private EmailLogRepository $emailLogRepository,
    ) {}

    public function __invoke(UpdateEmailStatusFromWebhook $updateEmailStatusFromWebhook)
    {
        $webhook = $updateEmailStatusFromWebhook->getWebhook();
        $this->emailLogRepository->updateStatusFromWebhook($webhook);
    }
}
