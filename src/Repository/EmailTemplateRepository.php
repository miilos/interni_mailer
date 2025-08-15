<?php

namespace App\Repository;

use App\Dto\EmailTemplateDto;
use App\Dto\SearchCriteria\EmailTemplateSearchCriteria;
use App\Entity\EmailTemplate;
use App\Service\EmailTemplateCreatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

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

    public function getTemplatesWithBodyTemplateId(int $bodyTemplateId): array
    {
        return $this->createQueryBuilder('template')
            ->where('template.bodyTemplate = :bodyTemplateId')
            ->setParameter('bodyTemplateId', $bodyTemplateId)
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

    public function updateEmailTemplate(EmailTemplate $emailTemplate, array $newValues): EmailTemplate
    {
        $allowedFields = ['subject', 'fromAddr', 'toAddr', 'cc', 'bcc', 'body', 'bodyTemplateName'];

        foreach ($newValues as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $setter = 'set'.ucfirst($key);
                $emailTemplate->$setter($value);
            }
        }

        if ($emailTemplate->getBodyTemplateName()) {
            $bodyTemplate = $this->creatorService->getBodyTemplateByName($emailTemplate->getBodyTemplateName());
            $emailTemplate->setBodyTemplate($bodyTemplate);
        }

        $this->entityManager->flush();
        return $emailTemplate;
    }

    public function deleteTemplatesWithBodyTemplateId(int $bodyTemplateId): void
    {
        $templates = $this->getTemplatesWithBodyTemplateId($bodyTemplateId);

        foreach ($templates as $template) {
            $this->entityManager->remove($template);
        }

        $this->entityManager->flush();
    }

    public function deleteTemplate(EmailTemplate $emailTemplate): void
    {
        $this->entityManager->remove($emailTemplate);
        $this->entityManager->flush();
    }
}
