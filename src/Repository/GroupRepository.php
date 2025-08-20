<?php

namespace App\Repository;

use App\Dto\GroupDto;
use App\Dto\SearchCriteria\GroupSearchCriteria;
use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 */
class GroupRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager
    )
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

    /**
     * @return Group[]
     */
    public function getGroupsByAddress(string $address): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.address LIKE :address')
            ->setParameter('address', '%'.$address.'%')
            ->getQuery()
            ->getResult();
    }

    public function buildSearch(GroupSearchCriteria $criteria): QueryBuilder
    {
        $qb = $this->createQueryBuilder('g');

        if ($criteria->getName()) {
            $qb->andWhere('g.name LIKE :name')
                ->setParameter('name', '%' . $criteria->getName() . '%');
        }

        $qb->addOrderBy('g.'.$criteria->getSortBy(), $criteria->getSortDirection());

        return $qb;
    }

    public function createGroup(GroupDto $groupDto): Group
    {
        $group = new Group();
        $group->setName($groupDto->getName());
        $group->setAddress($groupDto->getAddress());

        foreach ($groupDto->getRecipients() as $user) {
            $group->addUser($user);
        }

        $this->entityManager->persist($group);
        $this->entityManager->flush();
        return $group;
    }

    public function addUserToGroup(Group $group, User $user): Group
    {
        $group->addUser($user);
        $this->entityManager->flush();
        return $group;
    }

    public function removeUserFromGroup(Group $group, User $user): Group
    {
        $group->removeUser($user);
        $this->entityManager->flush();
        return $group;
    }

    public function deleteGroup(Group $group): void
    {
        $this->entityManager->remove($group);
        $this->entityManager->flush();
    }
}
