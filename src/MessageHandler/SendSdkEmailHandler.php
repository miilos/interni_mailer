<?php

namespace App\MessageHandler;

use App\Dto\EmailDto;
use App\Message\SendSdkEmail;
use App\Service\EmailBatchDispatcherService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

#[AsMessageHandler]
class SendSdkEmailHandler
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
        private EmailBatchDispatcherService $batchDispatcher,
    ) {}

    public function __invoke(SendSdkEmail $sendSdkEmail)
    {
        $emailDto = $this->objectMapper->map($sendSdkEmail, EmailDto::class);

        $this->batchDispatcher->batchSend($emailDto);
    }
}
