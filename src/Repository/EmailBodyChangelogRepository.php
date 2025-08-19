<?php

namespace App\Repository;

use App\Entity\EmailBody;
use App\Entity\EmailBodyChangelog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

/**
 * @extends ServiceEntityRepository<EmailBodyChangelog>
 */
class EmailBodyChangelogRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, EmailBodyChangelog::class);
        $this->entityManager = $entityManager;
    }

    public function createEmailBodyChangelog(EmailBody $updatedBody, array $diff): EmailBodyChangelog
    {
        $changelog = new EmailBodyChangelog();
        $changelog->setTemplate($updatedBody) ;
        $changelog->setName($updatedBody->getName());
        $changelog->setContent($updatedBody->getContent());
        $changelog->setExtension($updatedBody->getExtension());
        $changelog->setParsedBodyHtml($updatedBody->getParsedBodyHtml());
        $changelog->setVariables($updatedBody->getVariables());
        $changelog->setDiff($diff);
        $changelog->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($changelog);
        $this->entityManager->flush();
        return $changelog;
    }
}
