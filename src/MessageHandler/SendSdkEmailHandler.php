<?php

namespace App\MessageHandler;

use App\Dto\EmailDto;
use App\Message\SendSdkEmail;
use App\Service\EmailBatchDispatcherService;
use App\Service\EmailParser\EmailTemplateParserService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

#[AsMessageHandler]
class SendSdkEmailHandler
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
        private EmailBatchDispatcherService $batchDispatcher,
        private EmailTemplateParserService $emailTemplateParser,
    ) {}

    public function __invoke(SendSdkEmail $sendSdkEmail)
    {
        $emailDto = $this->objectMapper->map($sendSdkEmail, EmailDto::class);

        if ($emailDto->getEmailTemplate()) {
            $emailDto = $this->emailTemplateParser->parse($emailDto);
        }

        $this->batchDispatcher->batchSend($emailDto);
    }
}
