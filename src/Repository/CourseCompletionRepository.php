<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Course;
use App\Entity\CourseCompletion;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CourseCompletion>
 *
 * @method CourseCompletion|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourseCompletion|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourseCompletion[]    findAll()
 * @method CourseCompletion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseCompletionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseCompletion::class);
    }

    /**
     * Find all completions for a specific user
     *
     * @return CourseCompletion[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('cc')
            ->andWhere('cc.student = :user')
            ->setParameter('user', $user)
            ->orderBy('cc.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all completions for a specific course
     *
     * @return CourseCompletion[]
     */
    public function findByCourse(Course $course): array
    {
        return $this->createQueryBuilder('cc')
            ->andWhere('cc.course = :course')
            ->setParameter('course', $course)
            ->orderBy('cc.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find completed courses for a user
     *
     * @return CourseCompletion[]
     */
    public function findCompletedByUser(User $user): array
    {
        return $this->createQueryBuilder('cc')
            ->andWhere('cc.student = :user')
            ->andWhere('cc.completedAt IS NOT NULL')
            ->setParameter('user', $user)
            ->orderBy('cc.completedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find incomplete courses for a user
     *
     * @return CourseCompletion[]
     */
    public function findIncompleteByUser(User $user): array
    {
        return $this->createQueryBuilder('cc')
            ->andWhere('cc.student = :user')
            ->andWhere('cc.completedAt IS NULL')
            ->setParameter('user', $user)
            ->orderBy('cc.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find enrollment between a user and course
     */
    public function findOneByUserAndCourse(User $user, Course $course): ?CourseCompletion
    {
        return $this->createQueryBuilder('cc')
            ->andWhere('cc.student = :user')
            ->andWhere('cc.course = :course')
            ->setParameter('user', $user)
            ->setParameter('course', $course)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
