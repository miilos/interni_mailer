<?php

namespace App\Repository;

use App\Dto\EmailBodyDto;
use App\Entity\EmailBody;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailBody>
 */
class EmailBodyRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($registry, EmailBody::class);
        $this->entityManager = $entityManager;
    }

    public function getAllBodyTemplateNames(): array
    {
        return $this->createQueryBuilder('subject')
            ->select('subject.name')
            ->getQuery()
            ->getResult();
    }

    public function createEmailSubject(EmailBodyDto $subjectDto):  EmailBody
    {
        $emailSubject = new EmailBody();
        $emailSubject->setName($subjectDto->getName());
        $emailSubject->setContent($subjectDto->getContent());

        $this->entityManager->persist($emailSubject);
        $this->entityManager->flush();
        return $emailSubject;
    }
}
