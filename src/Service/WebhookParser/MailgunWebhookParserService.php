<?php

namespace App\Service\WebhookParser;

use App\Dto\WebhookDto;

class MailgunWebhookParserService implements WebhookParserInterface
{
    public function __construct(
        private string $webhookKey
    ) {}

    public function supports(array $webhook): bool
    {
        // verify mailgun signature
        $signatureObj = $webhook['signature'];

        if (!$signatureObj) {
            return false;
        }

        $token = $signatureObj['token'];
        $timestamp = $signatureObj['timestamp'];
        $signature = $signatureObj['signature'];

        $expectedSignature = hash_hmac('sha256', $timestamp.$token, $this->webhookKey);

        return hash_equals($expectedSignature, $signature);
    }

    public function parse(array $webhook): WebhookDto
    {
        $eventData = $webhook['event-data'];

        $headers = isset($eventData['message']['headers'])
            ? array_change_key_case($eventData['message']['headers'], CASE_LOWER)
            : [];
        $emailId = $headers['x-internal-email-id'] ?? $headers['x-mailgun-internal-email-id'] ?? null;

        return new WebhookDto(
            event: $eventData['event'],
            recipient: $eventData['recipient'],
            emailId: $emailId,
            error: $eventData['delivery-status']['description'] ?? null,
        );
    }
}
