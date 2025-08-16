<?php

namespace App\Message;

use App\Dto\WebhookDto;

class UpdateEmailStatusFromWebhook
{
    public function __construct(
        private WebhookDto $webhook,
    ) {}

    public function getWebhook(): WebhookDto
    {
        return $this->webhook;
    }
}
