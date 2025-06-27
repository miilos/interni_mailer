<?php

namespace App\Repository;

use App\Dto\EmailLogDto;
use App\Entity\EmailLog;
use App\Entity\EmailStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
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

        $this->entityManager->persist($log);
        $this->entityManager->flush();
        return $log;
    }
}
