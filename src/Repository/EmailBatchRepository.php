<?php

namespace App\Repository;

use App\Dto\EmailDto;
use App\Entity\EmailBatch;
use App\Entity\EmailBatchStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mime\Email;

/**
 * @extends ServiceEntityRepository<EmailBatch>
 */
class EmailBatchRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, EmailBatch::class);
        $this->entityManager = $entityManager;
    }

    public function getBatchById(string $batchId): ?EmailBatch
    {
        return $this->createQueryBuilder('batch')
            ->where('batch.batchId = :batchId')
            ->setParameter('batchId', $batchId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getFailedBatches(): array
    {
        return $this->createQueryBuilder('batch')
            ->where('batch.status = :status')
            ->setParameter('status', EmailBatchStatusEnum::FAILED->value)
            ->getQuery()
            ->getResult();
    }

    public function createBatch(string $batchId, EmailDto $emailDto): EmailBatch
    {
        $batch = new EmailBatch();
        $batch->setBatchId($batchId);
        $batch->setEmailId($emailDto->getId());
        $batch->setSubject($emailDto->getSubject());
        $batch->setFromAddr($emailDto->getFrom());
        $batch->setToAddr($emailDto->getTo());
        $batch->setCc($emailDto->getCc());
        $batch->setBcc($emailDto->getBcc());
        $batch->setBody($emailDto->getBody());
        $batch->setStatus(EmailBatchStatusEnum::QUEUED);
        $batch->setDispatchedAt(new \DateTimeImmutable());
        $batch->setBodyTemplate($emailDto->getBodyTemplate());
        $batch->setEmailTemplate($emailDto->getEmailTemplate());

        $this->entityManager->persist($batch);
        $this->entityManager->flush();
        return $batch;
    }

    public function updateBatchStatus(string $batchStatus, string $batchId): int
    {
        return $this->createQueryBuilder('batch')
            ->update()
            ->set('batch.status', ':batchStatus')
            ->set('batch.numFailedResends', 0)
            ->andWhere('batch.batchId = :batchId')
            ->setParameter('batchId', $batchId)
            ->setParameter('batchStatus', $batchStatus)
            ->getQuery()
            ->execute();
    }

    public function updateFailedResends(string $batchId): int
    {
        return $this->createQueryBuilder('batch')
            ->update()
            ->set('batch.numFailedResends', 'batch.numFailedResends + 1')
            ->andWhere('batch.batchId = :batchId')
            ->setParameter('batchId', $batchId)
            ->getQuery()
            ->execute();
    }

    public function updateError(string $batchId, string $error): int
    {
        return $this->createQueryBuilder('batch')
            ->update()
            ->set('batch.error', ':error')
            ->andWhere('batch.batchId = :batchId')
            ->setParameter('batchId', $batchId)
            ->setParameter('error', $error)
            ->getQuery()
            ->execute();
    }

    public function deleteBatch(EmailBatch $batch): void
    {
        $this->entityManager->remove($batch);
        $this->entityManager->flush();
    }
}
