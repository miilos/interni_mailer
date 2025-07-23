<?php

namespace App\Repository;

use App\Dto\EmailLogDto;
use App\Dto\LogSearchCriteria;
use App\Entity\EmailLog;
use App\Entity\EmailStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

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
        $log->setCC($emailLog->getCc());
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

    public function findByCriteria(LogSearchCriteria $criteria): array
    {
        return $this->buildSearch($criteria)
            ->getQuery()
            ->getResult();
    }
}
