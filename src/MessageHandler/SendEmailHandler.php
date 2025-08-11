<?php

namespace App\MessageHandler;

use App\Dto\EmailDto;
use App\Entity\EmailBatchStatusEnum;
use App\Entity\EmailStatusEnum;
use App\Message\LogEmail;
use App\Message\SendEmail;
use App\Repository\EmailBatchRepository;
use App\Service\EmailSenderService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

#[AsMessageHandler]
class SendEmailHandler
{
    public function __construct(
        private EmailSenderService $sender,
        private MessageBusInterface $messageBus,
        private EmailBatchRepository $batchRepository,
        private ObjectMapperInterface $objectMapper,
    ) {}

    public function __invoke(SendEmail $sendEmail)
    {
        $batchId = $sendEmail->getBatchId();
        $batch = $this->batchRepository->getBatchById($batchId);

        // rebuild the dto based on the data in the batch record in the db
        $emailDto = $this->objectMapper->map($batch, EmailDto::class);

        $this->sender->send($emailDto);

        $this->batchRepository->updateBatchStatus(EmailBatchStatusEnum::SENT->value, $batchId);

        $this->messageBus->dispatch(new LogEmail($emailDto, EmailStatusEnum::SENT->value));
    }
}
