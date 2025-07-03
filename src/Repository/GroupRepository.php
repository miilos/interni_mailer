<?php

namespace App\Repository;

use App\Dto\GroupDto;
use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
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
}
