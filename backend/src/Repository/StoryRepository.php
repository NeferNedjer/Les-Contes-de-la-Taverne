<?php

namespace App\Repository;

use App\Entity\Story;
use App\Entity\StoryLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Story>
 */
class StoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Story::class);
    }

    //    /**
    //     * @return Story[] Returns an array of Story objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Story
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findTopStoryOfCurrentMonth(): ?Story
    {
        $now = new \DateTimeImmutable('now');
        $start = $now->modify('first day of this month')->setTime(0, 0, 0);
        $end = $now->modify('last day of this month')->setTime(23, 59, 59);

        $qb = $this->createQueryBuilder('s')
            ->leftJoin(StoryLike::class, 'l', 'WITH', 'l.story = s')
            ->andWhere('s.status = :published')
            ->andWhere('(l.createdAt BETWEEN :start AND :end) OR l.id IS NULL')
            ->setParameter('published', 'published')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->groupBy('s.id')
            ->orderBy('COUNT(l.id)', 'DESC')
            ->addOrderBy('s.createdAt', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
