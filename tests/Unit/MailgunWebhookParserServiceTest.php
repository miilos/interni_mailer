<?php

namespace App\Tests\Unit;

use App\Service\WebhookParser\MailgunWebhookParserService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MailgunWebhookParserServiceTest extends TestCase
{
    public static function parseDtoProvider(): array
    {
        return [
            [
                [
                    'signature' => [
                        'token' => '862b1aaffd7e2c68a0f41aac21d8896fc2f0eff71978cfe8ee',
                        'timestamp' => '1755447882',
                        'signature' => '02c751d0a35fc1ca3a5194a5469e85f43e914f6775d2ebb32c2872a834a27063'
                    ],
                    'event-data' => [
                        'id' => 'CPgfbmQMTCKtHW6uIWtuVe',
                        'timestamp' => 1521472262.908181,
                        'log-level' => 'info',
                        'event' => 'delivered',
                        'delivery-status' => [
                            'tls' => true,
                            'mx-host' => 'smtp-in.example.com',
                            'code' => 250,
                            'description' => '',
                            'session-seconds' => 0.4331989288330078,
                            'utf8' => true,
                            'attempt-no' => 1,
                            'message' => 'OK',
                            'certificate-verified' => true
                        ],
                        'flags' => [
                            'is-routed' => false,
                            'is-authenticated' => true,
                            'is-system-test' => false,
                            'is-test-mode' => false
                        ],
                        'envelope' => [
                            'transport' => 'smtp',
                            'sender' => 'bob@sandbox7399aac03b154a6b888f9156b08b17c4.mailgun.org',
                            'sending-ip' => '209.61.154.250',
                            'targets' => 'alice@example.com'
                        ],
                        'message' => [
                            'headers' => [
                                'to' => 'Alice <alice@example.com>',
                                'message-id' => '20130503182626.18666.16540@sandbox7399aac03b154a6b888f9156b08b17c4.mailgun.org',
                                'from' => 'Bob <bob@sandbox7399aac03b154a6b888f9156b08b17c4.mailgun.org>',
                                'subject' => 'Test delivered webhook',
                                'X-Internal-Email-Id' => '123abcd',
                                'X-Mailgun-Internal-Email-Id' => '123abcd'
                            ],
                            'attachments' => [],
                            'size' => 111
                        ],
                        'recipient' => 'alice@example.com',
                        'recipient-domain' => 'example.com',
                        'storage' => [
                            'url' => 'https://se.api.mailgun.net/v3/domains/sandbox7399aac03b154a6b888f9156b08b17c4.mailgun.org/messages/message_key',
                            'key' => 'message_key'
                        ],
                        'campaigns' => [],
                        'tags' => [
                            'my_tag_1',
                            'my_tag_2'
                        ],
                        'user-variables' => [
                            'my_var_1' => 'Mailgun Variable #1',
                            'my-var-2' => 'awesome'
                        ]
                    ]
                ]
            ]
        ];
    }

    #[DataProvider('parseDtoProvider')]
    public function testParsesDto(array $webhook): void
    {
        $mailgunParser = new MailgunWebhookParserService('test-key');

        $webhookDto = $mailgunParser->parse($webhook);
        $this->assertSame('delivered', $webhookDto->getEvent());
        $this->assertSame('alice@example.com', $webhookDto->getRecipient());
        $this->assertSame('123abcd', $webhookDto->getEmailId());
    }
}
