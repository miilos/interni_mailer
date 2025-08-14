<?php

namespace App\Scheduler\Handler;

use App\Message\SendEmail;
use App\Repository\EmailBatchRepository;
use App\Scheduler\Message\ResendFailedBatches;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class ResendFailedBatchesHandler
{
    public function __construct(
        private EmailBatchRepository $emailBatchRepository,
        private MessageBusInterface $messageBus,
    ) {}

    public function __invoke(ResendFailedBatches $message)
    {
        $failedBatches = $this->emailBatchRepository->getFailedBatches();

        foreach ($failedBatches as $batch) {
            if ($batch->getNumFailedResends() >= 3) {
                $this->emailBatchRepository->deleteBatch($batch);
                continue;
            }

            $this->messageBus->dispatch(new SendEmail($batch->getBatchId()));
        }
    }
}
