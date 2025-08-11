<?php

namespace App\EventListener;

use App\Dto\EmailDto;
use App\Entity\EmailBatchStatusEnum;
use App\Entity\EmailStatusEnum;
use App\Message\LogEmail;
use App\Message\SendEmail;
use App\Repository\EmailBatchRepository;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

#[AsEventListener]
final class MailerWorkerFailureListener
{
    public function __construct(
        private EmailBatchRepository $batchRepository,
        private MessageBusInterface $messageBus,
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

        $batch = $this->batchRepository->getBatchById($batchId);
        $emailDto = $this->objectMapper->map($batch, EmailDto::class);

        $error = $event->getThrowable()->getMessage();

        $this->messageBus->dispatch(new LogEmail($emailDto, EmailStatusEnum::FAILED->value, $error));
    }
}
