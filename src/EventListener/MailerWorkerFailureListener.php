<?php

namespace App\EventListener;

use App\Dto\EmailDto;
use App\Entity\EmailBatchStatusEnum;
use App\Message\SendEmail;
use App\Repository\EmailBatchRepository;
use App\Service\EmailLoggerService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

#[AsEventListener]
final class MailerWorkerFailureListener
{
    public function __construct(
        private EmailBatchRepository $batchRepository,
        private EmailLoggerService $emailLogger,
        private ObjectMapperInterface $objectMapper,
    ) {}

    public function __invoke(WorkerMessageFailedEvent $event): void
    {
        $message = $event->getEnvelope()->getMessage();

        if (!$message instanceof SendEmail) {
            return;
        }

        $batchId = $message->getBatchId();
        $this->batchRepository->updateBatchStatus(EmailBatchStatusEnum::FAILED->value, $batchId);
        $this->batchRepository->updateFailedResends($batchId);

        $error = $event->getThrowable()->getMessage();
        $this->batchRepository->updateError($batchId, $error);

        $batch = $this->batchRepository->getBatchById($batchId);
        $emailDto = $this->objectMapper->map($batch, EmailDto::class);
        $this->emailLogger->logBatchSendingError($emailDto, $error);
    }
}
