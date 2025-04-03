<?php

namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    /**
     * Get all likes for a specific post.
     *
     * @param int $postId
     * @return Like[]
     */
    public function findLikesByPostId(int $postId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.post = :postId')
            ->setParameter('postId', $postId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Check if a like exists for a specific post and user.
     *
     * @param int $postId
     * @param int $userId
     * @return Like|null
     */
    public function findLikeByPostAndUser(int $postId, int $userId): ?Like
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.post = :postId')
            ->andWhere('l.user = :userId')
            ->setParameter('postId', $postId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Delete all likes for a specific post.
     *
     * @param int $postId
     * @return void
     */
    public function deleteLikesByPostId(int $postId): void
    {
        $this->createQueryBuilder('l')
            ->delete()
            ->andWhere('l.post = :postId')
            ->setParameter('postId', $postId)
            ->getQuery()
            ->execute();
    }
}
