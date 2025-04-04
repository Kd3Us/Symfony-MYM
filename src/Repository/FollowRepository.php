<?php

namespace App\Repository;

use App\Entity\Follow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Follow>
 */
class FollowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Follow::class);
    }

    /**
     * Get all followers of a user by their ID.
     *
     * @param int $userId
     * @return Follow[]
     */
    public function findFollowersByUserId(int $userId): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.followed = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all users followed by a user by their ID.
     *
     * @param int $userId
     * @return Follow[]
     */
    public function findFollowingByUserId(int $userId): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.follower = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Check if a user is already following another user.
     *
     * @param int $followerId
     * @param int $followedUserId
     * @return Follow|null
     */
    public function findFollowRelation(int $followerId, int $followedUserId): ?Follow
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.follower = :followerId')
            ->andWhere('f.followedUser = :followedUserId')
            ->setParameter('followerId', $followerId)
            ->setParameter('followedUserId', $followedUserId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
