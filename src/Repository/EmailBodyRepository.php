<?php

namespace App\Repository;

use App\Dto\EmailBodyDto;
use App\Dto\SearchCriteria\BodyTemplateSearchCriteria;
use App\Entity\EmailBody;
use App\Service\EmailParser\MjmlBodyParserService;
use App\Service\EmailParser\TwigBodyParserService;
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
    private TwigBodyParserService $twigBodyParser;
    private MjmlBodyParserService $mjmlBodyParser;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager,
        TwigBodyParserService $twigBodyParser,
        MjmlBodyParserService $mjmlBodyParser
    )
    {
        parent::__construct($registry, EmailBody::class);
        $this->entityManager = $entityManager;
        $this->twigBodyParser = $twigBodyParser;
        $this->mjmlBodyParser = $mjmlBodyParser;
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

        switch ($bodyDto->getExtension()) {
            case 'html':
                $emailBody->setParsedBodyHtml($bodyDto->getContent());
                break;
            case 'html.twig':
                $emailBody->setParsedBodyHtml($this->twigBodyParser->parseTemplate($bodyDto->getContent()));
                break;
            case 'mjml.html':
                $emailBody->setParsedBodyHtml($this->mjmlBodyParser->parseTemplate($bodyDto->getContent()));
                break;
            default:
                break;
        }

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
}
