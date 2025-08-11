<?php

namespace App\Service;

use App\Dto\EmailDto;
use App\Message\SendEmail;
use App\Repository\EmailBatchRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailBatchDispatcherService
{
    private const BATCH_SIZE = 1000;

    public function __construct(
        private EmailBatchRepository $emailBatchRepository,
        private MessageBusInterface $messageBus,
    ) {}

    public function batchSend(EmailDto $emailDto): void
    {
        $batches = array_chunk($emailDto->getTo(), self::BATCH_SIZE);

        foreach ($batches as $batch) {
            $batchId = Uuid::uuid4()->toString();
            // clone the email dto data so that the message handler has access to all the data needed to send the email
            $batchEmailData = clone $emailDto;
            $batchEmailData->setTo($batch);
            $this->emailBatchRepository->createBatch($batchId, $batchEmailData);

            $this->messageBus->dispatch(new SendEmail($batchId));
        }
    }
}
