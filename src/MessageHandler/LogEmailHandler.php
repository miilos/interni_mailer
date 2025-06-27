<?php

namespace App\MessageHandler;

use App\Message\LogEmail;
use App\Repository\EmailLogRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LogEmailHandler
{
    public function __construct(
        private EmailLogRepository $emailLogRepository,
    ) {}

    public function __invoke(LogEmail $logEmail)
    {
        $emailLogDto = $logEmail->getEmailLogDto();
        $this->emailLogRepository->createEmailLog($emailLogDto);
    }
}
