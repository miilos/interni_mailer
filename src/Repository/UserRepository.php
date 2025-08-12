<?php

namespace App\Repository;

use App\Dto\SearchCriteria\UserSearchCriteria;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getUsersFromList(array $emails): array
    {
        $users = [];

        foreach ($emails as $email) {
            $user = $this->getUserById($email);

            if ($user) {
                $users[] = $user;
            }
        }

        return $users;
    }

    public function getUserById($id): ?User
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function buildSearch(UserSearchCriteria $criteria): QueryBuilder
    {
        $qb = $this->createQueryBuilder('user');

        if ($criteria->getUsername()) {
            $qb->orWhere('LOWER(user.username) LIKE :username')
                ->setParameter('username', '%' . strtolower($criteria->getUsername()) . '%');
        }

        if ($criteria->getFirstName()) {
            $qb->orWhere('LOWER(user.firstname) LIKE :firstName')
                ->setParameter('firstName', '%' . strtolower($criteria->getFirstName()) . '%');
        }

        if ($criteria->getLastName()) {
            $qb->orWhere('LOWER(user.lastname) LIKE :lastName')
                ->setParameter('lastName', '%' . strtolower($criteria->getLastName()) . '%');
        }

        if ($criteria->getEmail()) {
            $qb->orWhere('LOWER(user.email) LIKE :email')
                ->setParameter('email', '%' . strtolower($criteria->getEmail()) . '%');
        }

        return $qb;
    }
}
