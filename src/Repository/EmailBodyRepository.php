<?php

namespace App\Repository;

use App\Dto\EmailBodyDto;
use App\Dto\SearchCriteria\BodyTemplateSearchCriteria;
use App\Entity\EmailBody;
use App\Service\EmailParser\BodyParser\BodyParserService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailBody>
 */
class EmailBodyRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;
    private BodyParserService $bodyParser;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager,
        BodyParserService $bodyParser,
    )
    {
        parent::__construct($registry, EmailBody::class);
        $this->entityManager = $entityManager;
        $this->bodyParser = $bodyParser;
    }

    public function getAllBodyTemplateNames(): array
    {
        return $this->createQueryBuilder('subject')
            ->select('subject.name')
            ->getQuery()
            ->getResult();
    }

    public function createEmailBody(EmailBodyDto $bodyDto):  EmailBody
    {
        $emailBody = new EmailBody();
        $emailBody->setName($bodyDto->getName());
        $emailBody->setContent($bodyDto->getContent());
        $emailBody->setExtension($bodyDto->getExtension());
        $emailBody->setParsedBodyHtml(
            $this->bodyParser->parse($bodyDto->getContent(), $bodyDto->getExtension(), $bodyDto->getVariables())
        );
        $emailBody->setVariables($bodyDto->getVariables());

        $this->entityManager->persist($emailBody);
        $this->entityManager->flush();
        return $emailBody;
    }

    public function buildSearch(BodyTemplateSearchCriteria $criteria): QueryBuilder
    {
        $qb = $this->createQueryBuilder('template');

        if ($criteria->getName()) {
            $qb->andWhere('template.name LIKE :name')
                ->setParameter('name', '%' . $criteria->getName() . '%');
        }

        if ($criteria->getFormat()) {
            $qb->andWhere('template.format = :format')
                ->setParameter('format', $criteria->getFormat());
        }

        return $qb;
    }

    public function deleteBodyTemplate(EmailBody $body): void
    {
        $this->entityManager->remove($body);
        $this->entityManager->flush();
    }
}
