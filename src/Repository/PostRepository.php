<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Post::class);
    }

    public function paginateAll(Request $request): PaginationInterface
    {
        $builder = $this->createQueryBuilder('p')
            ->select('p as post', 'c', 'COUNT(cm.id) as totalComments')
            ->leftJoin('p.comments', 'cm')
            ->leftJoin('p.category', 'c')
            ->groupBy('p.id', 'c.id')
            ->orderBy('p.createdAt', 'DESC');
        return $this->paginator->paginate(
            $builder,
            $request->query->getInt('page', 1),
            10
        );
    }

    public function findByService($serviceId)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.comments', 'com')
            ->addSelect('c')
            ->addSelect('COUNT(com.id) as totalComments')
            ->groupBy('p.id')
            ->orderBy('p.createdAt', 'DESC');

        if ($serviceId !== 'all') {
            $qb->where('p.category = :serviceId')
                ->setParameter('serviceId', $serviceId);
        }

        $results = $qb->getQuery()->getResult();

        return array_map(function ($result) {
            return [
                'post' => $result[0],
                'totalComments' => $result['totalComments'],
            ];
        }, $results);
    }



    //    /**
    //     * @return Post[] Returns an array of Post objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
