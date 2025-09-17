<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function findLastPublishedCourses(int $duration=2): Paginator{
        $queryBuilder = $this->createQueryBuilder('c')
        ->addSelect('ca')
        ->addSelect('f')
        ->leftJoin('c.category', 'ca')
        ->leftJoin('c.trainers', 'f')
        ->andWhere('c.duration > :duration')
        ->andWhere('c.published = true')
        ->addOrderBy('c.dateCreated', 'DESC')
        ->setParameter('duration', $duration)
        ->setMaxResults(5);
        $query = $queryBuilder->getQuery();
        return new Paginator($query, true);
    }

}
