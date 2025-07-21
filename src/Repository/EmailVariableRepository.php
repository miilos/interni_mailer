<?php

namespace App\Repository;

use App\Dto\EmailVariableDto;
use App\Entity\EmailVariable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailVariable>
 */
class EmailVariableRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($registry, EmailVariable::class);
        $this->entityManager = $entityManager;
    }

    public function createEmailVariable(EmailVariableDto $dto): EmailVariable
    {
        $variable =  new EmailVariable();
        $variable->setName($dto->getName());
        $variable->setValue($dto->getValue());

        $this->entityManager->persist($variable);
        $this->entityManager->flush();
        return $variable;
    }
}
