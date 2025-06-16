<?php

namespace App\Repository;

use App\Dto\EmailTwigTemplateDto;
use App\Entity\EmailTwigTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailTwigTemplate>
 */
class EmailTwigTemplateRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, EmailTwigTemplate::class);
        $this->entityManager = $entityManager;
    }

    public function getEmailTwigTemplateByName(string $name): ?array
    {
        return $this->createQueryBuilder('template')
            ->select('template.filePath')
            ->where('template.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAllEmailTwigTemplateNames(): array
    {
        return $this->createQueryBuilder('template')
            ->select('template.name')
            ->getQuery()
            ->getResult();
    }

    public function createTemplate(EmailTwigTemplateDto $templateDto): EmailTwigTemplate
    {
        $twigTemplate = new EmailTwigTemplate();
        $twigTemplate->setName($templateDto->getName());
        $twigTemplate->setFilePath($templateDto->getFilePath());

        $this->entityManager->persist($twigTemplate);
        $this->entityManager->flush();
        return $twigTemplate;
    }
}
