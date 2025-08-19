<?php

namespace App\Repository;

use App\Dto\EmailLogDto;
use App\Dto\SearchCriteria\LogSearchCriteria;
use App\Dto\WebhookDto;
use App\Entity\EmailLog;
use App\Entity\EmailStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Core\DateTime;

/**
 * @extends ServiceEntityRepository<EmailLog>
 */
class EmailLogRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, EmailLog::class);
        $this->entityManager = $entityManager;
    }

    public function createEmailLog(EmailLogDto $emailLog): EmailLog
    {
        $log = new EmailLog();
        $log->setEmailId($emailLog->getEmailId());
        $log->setSubject($emailLog->getSubject());
        $log->setFromAddr($emailLog->getFrom());
        $log->setToAddr($emailLog->getTo());
        $log->setCc($emailLog->getCc());
        $log->setBcc($emailLog->getBcc());
        $log->setBody($emailLog->getBody());
        $log->setStatus(EmailStatusEnum::from($emailLog->getStatus()));
        $log->setLoggedAt($emailLog->getLoggedAt());
        $log->setError($emailLog->getError());
        $log->setBodyTemplate($emailLog->getBodyTemplateName());
        $log->setEmailTemplate($emailLog->getEmailTemplate());

        $this->entityManager->persist($log);
        $this->entityManager->flush();
        return $log;
    }

    public function buildFindAll(): QueryBuilder
    {
        return $this->createQueryBuilder('log');
    }

    public function buildSearch(LogSearchCriteria $criteria): QueryBuilder
    {
        $qb = $this->createQueryBuilder('log');

        if ($criteria->getSubject()) {
            $qb->andWhere('log.subject LIKE :subject')
                ->setParameter('subject', '%' . $criteria->getSubject() . '%');
        }

        if ($criteria->getFrom()) {
            $qb->andWhere('log.fromAddr LIKE :from')
                ->setParameter('from', '%' . $criteria->getFrom() . '%');
        }

        if ($criteria->getTo()) {
            $qb->andWhere('log.toAddr LIKE :to')
                ->setParameter('to', '%' . $criteria->getTo() . '%');
        }

        if ($criteria->getStatus()) {
            $qb->andWhere('log.status = :status')
                ->setParameter('status', $criteria->getStatus());
        }

        if ($criteria->getBodyTemplate()) {
            $qb->andWhere('log.bodyTemplate LIKE :bodyTemplate')
                ->setParameter('bodyTemplate', '%' . $criteria->getBodyTemplate() . '%');
        }

        if ($criteria->getEmailTemplate()) {
            $qb->andWhere('log.emailTemplate LIKE :emailTemplate')
                ->setParameter('emailTemplate', '%' . $criteria->getEmailTemplate() . '%');
        }

        $qb->addOrderBy('log.'.$criteria->getSortBy(), $criteria->getSortDirection());

        return $qb;
    }

    public function updateStatusFromWebhook(WebhookDto $webhookDto): int
    {
        $qb = $this->createQueryBuilder('log')
            ->update()
            ->set('log.status', ':status')
            ->setParameter('status', EmailStatusEnum::from($webhookDto->getEvent()));

        if ($webhookDto->getError()) {
            $qb->set('log.error', ':error')
                ->setParameter('error', $webhookDto->getError());
        }

        $qb->andWhere('log.emailId LIKE :emailId')
            ->setParameter('emailId', '%'.$webhookDto->getEmailId().'%')
            ->andWhere('log.toAddr LIKE :recipient')
            ->setParameter('recipient', '%'.$webhookDto->getRecipient().'%');

        return $qb->getQuery()->execute();
    }

    public function getTotalEmails(string $period): int
    {
        return $this->createQueryBuilder('log')
            ->select('COUNT(log.id)')
            ->andWhere('log.loggedAt > :date')
            ->setParameter('date', new \DateTime('-1'.$period))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function calculateNumEmailsByStatus(string $period): array
    {
        return $this->createQueryBuilder('log')
            ->select('COUNT(log.id) as count, log.status')
            ->andWhere('log.loggedAt > :date')
            ->setParameter('date', new \DateTime('-1'.$period))
            ->groupBy('log.status')
            ->getQuery()
            ->getResult();
    }

    public function getMostUsedEmailTemplates(string $period): array
    {
        return $this->createQueryBuilder('log')
            ->select('log.emailTemplate as templateName, COUNT(log.emailTemplate) as count')
            ->andWhere('LENGTH(log.emailTemplate) > 0')
            ->andWhere('log.loggedAt > :date')
            ->setParameter('date', new \DateTime('-1'.$period))
            ->groupBy('log.emailTemplate')
            ->addOrderBy('count', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    public function getMostUsedBodyTemplates(string $period): array
    {
        return $this->createQueryBuilder('log')
            ->select('log.bodyTemplate as templateName, COUNT(log.bodyTemplate) as count')
            ->andWhere('LENGTH(log.bodyTemplate) > 0')
            ->andWhere('log.loggedAt > :date')
            ->setParameter('date', new \DateTime('-1'.$period))
            ->groupBy('log.bodyTemplate')
            ->addOrderBy('count', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }
}
