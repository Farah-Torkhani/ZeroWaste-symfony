<?php

namespace App\Repository;

use App\Entity\DonHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DonHistory>
 *
 * @method DonHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method DonHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method DonHistory[]    findAll()
 * @method DonHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DonHistory::class);
    }

    public function save(DonHistory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

  

    public function remove(DonHistory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getTotalDonations(int $fund) {
        return $this->createQueryBuilder('d')
        ->select('SUM(d.donation_price)')
        ->where('d.fundsID = :fundrising')
        ->setParameter('fundrising', $fund)
        ->getQuery()
        ->getSingleScalarResult();
    }
    

//    /**
//     * @return DonHistory[] Returns an array of DonHistory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DonHistory
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function findTopDonator()
{
    $qb = $this->createQueryBuilder('cp')
        ->select('p.id')
        ->join('cp.don', 'p')
        ->groupBy('p.Id')
        ->orderBy('don_count', 'DESC')
        ->setMaxResults(1);
    
    return $qb->getQuery()->getOneOrNullResult();
}
}
