<?php

namespace App\Repository;

use App\Dto\EmailTemplateDto;
use App\Entity\EmailTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;

/**
 * @extends ServiceEntityRepository<EmailTemplate>
 */
class EmailTemplateRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, EmailTemplate::class);
        $this->entityManager = $entityManager;
    }

    public function getAllEmailTemplateNames(): array
    {
        return $this->createQueryBuilder('template')
            ->select('template.name')
            ->getQuery()
            ->getResult();
    }

    public function createEmailTemplate(EmailTemplateDto  $emailTemplateDto): EmailTemplate
    {
        $template = new EmailTemplate();

        if ($emailTemplateDto->getName()) {
            $template->setName($emailTemplateDto->getName());
        }
        else {
            $faker = Factory::create('en_US');
            $name = implode('-', $faker->words(2)) . '-' . time();
            $template->setName($name);
        }

        $template->setSubject($emailTemplateDto->getSubject());
        $template->setFromAddr($emailTemplateDto->getFrom());
        $template->setToAddr($emailTemplateDto->getTo());
        $template->setCc($emailTemplateDto->getCc());
        $template->setBcc($emailTemplateDto->getBcc());
        $template->setBody($emailTemplateDto->getBody());
        $template->setCreatedAt(new \DateTimeImmutable());
        $template->setTwigTemplateName($emailTemplateDto->getTwigTemplateName());

        $this->entityManager->persist($template);
        $this->entityManager->flush();

        return $template;
    }
}
