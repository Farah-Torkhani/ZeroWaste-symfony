<?php

namespace App\Repository;

use App\Entity\CommandsProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommandsProduit>
 *
 * @method CommandsProduit|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandsProduit|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandsProduit[]    findAll()
 * @method CommandsProduit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandsProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandsProduit::class);
    }

    public function add(CommandsProduit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CommandsProduit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CommandsProduit[] Returns an array of CommandsProduit objects
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

//    public function findOneBySomeField($value): ?CommandsProduit
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


    public function getCommandesNumber($commande_id)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('select count(c) from APP\Entity\CommandsProduit c where c.commande = :id ');
        $query->setParameter('id', $commande_id);
        return $query->getSingleScalarResult();
    }


}
