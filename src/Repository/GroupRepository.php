<?php

namespace App\Repository;

use App\Dto\GroupDto;
use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 */
class GroupRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Group::class);
        $this->entityManager = $entityManager;
    }

    public function getAllGroups(): array
    {
        return $this->createQueryBuilder('g')
            ->getQuery()
            ->getResult();
    }

    public function getAllGroupNames(): array
    {
        return $this->createQueryBuilder('g')
            ->select('g.name')
            ->getQuery()
            ->getResult();
    }

    public function getGroupByAddress(string $address): ?Group
    {
        return $this->createQueryBuilder('g')
            ->where('g.address = :address')
            ->setParameter('address', $address)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function createGroup(GroupDto $groupDto): Group
    {
        $group = new Group();
        $group->setName($groupDto->getName());
        $group->setAddress($groupDto->getAddress());
        $group->setRecipients($groupDto->getRecipients());

        $this->entityManager->persist($group);
        $this->entityManager->flush();
        return $group;
    }
}
