<?php

namespace App\Repository;

use App\Entity\Collecte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Collecte>
 *
 * @method Collecte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Collecte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collecte[]    findAll()
 * @method Collecte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollecteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collecte::class);
    }

    public function save(Collecte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Collecte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Collecte[] Returns an array of Collecte objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Collecte
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


    /**
     * Returns all Annonces per page
     * @return void 
     */
    public function getPaginatedAnnonces($page, $limit, $filters = null){
        $query = $this->createQueryBuilder('a')
            ->where('a.active = 1');

        // On filtre les données
        if($filters != null){
            $query->andWhere('a.categories IN(:cats)')
                ->setParameter(':cats', array_values($filters));
        }

        $query->orderBy('a.created_at')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * Returns number of Annonces
     * @return void 
     */
    public function getTotalAnnonces($filters = null){
        $query = $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.active = 1');
        // On filtre les données
        if($filters != null){
            $query->andWhere('a.categories IN(:cats)')
                ->setParameter(':cats', array_values($filters));
        }

        return $query->getQuery()->getSingleScalarResult();
    }
    

    public function filterCategories($category){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p from App\Entity\Collecte p where p.collecteCategorie =:id')->setParameter('id', $category);
        return $query->getResult();
    }

    public function findAllOrderByDateDeb()
{
    $qb = $this->createQueryBuilder('c')
        ->orderBy('c.date_deb', 'ASC');

    return $qb->getQuery()->getResult();
}



}
