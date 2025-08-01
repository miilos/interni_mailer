<?php

namespace App\Repository;

use App\Dto\EmailTemplateDto;
use App\Dto\SearchCriteria\EmailTemplateSearchCriteria;
use App\Dto\SearchCriteria\SearchCriteria;
use App\Entity\EmailBody;
use App\Entity\EmailTemplate;
use App\Service\EmailTemplateCreatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;

/**
 * @extends ServiceEntityRepository<EmailTemplate>
 */
class EmailTemplateRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;
    private EmailTemplateCreatorService $creatorService;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, EmailTemplateCreatorService $creatorService)
    {
        parent::__construct($registry, EmailTemplate::class);
        $this->entityManager = $entityManager;
        $this->creatorService = $creatorService;
    }

    public function getAllEmailTemplateNames(): array
    {
        return $this->createQueryBuilder('template')
            ->select('template.name')
            ->getQuery()
            ->getResult();
    }

    public function buildSearch(EmailTemplateSearchCriteria $criteria): QueryBuilder
    {
        $qb = $this->createQueryBuilder('template')
            ->leftJoin('template.bodyTemplate', 'bodyTemplate')
            ->addSelect('bodyTemplate');

        if ($criteria->getName()) {
            $qb->andWhere('template.name LIKE :name')
                ->setParameter('name', '%' . $criteria->getName() . '%');
        }

        return $qb;
    }

    public function createEmailTemplate(EmailTemplateDto  $emailTemplateDto): EmailTemplate
    {
        $template = $this->creatorService->createTemplateWithBodyName($emailTemplateDto);
        $this->entityManager->persist($template);
        $this->entityManager->flush();
        return $template;
    }
}
