<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function save(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Produit[] Returns an array of Produit objects
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

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    
    public function filterCategories($category){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p from App\Entity\Produit p where p.categorieProduit =:id')->setParameter('id', $category);
        return $query->getResult();
    }

    public function searchProductFunction($value){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p from App\Entity\Produit p where p.nomProduit like :value or p.description like :value')->setParameter('value', '%'.$value.'%');
        return $query->getResult();
    }

    public function searchProductByImage($etiquette, $score){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p from App\Entity\Produit p where p.etiquette =:etiquette and p.score =:score ')->setParameter('etiquette', $etiquette)->setParameter('score', $score);
        return $query->getResult();
    }

    public function searchSimilairesProductByImage($etiquette){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT p from App\Entity\Produit p where p.etiquette =:etiquette ')->setParameter('etiquette', $etiquette);
        return $query->getResult();
    }

    

}
