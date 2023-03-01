<?php

namespace App\Repository;

use App\Entity\Fundrising;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Twilio\Rest\Client;

/**
 * @extends ServiceEntityRepository<Fundrising>
 *
 * @method Fundrising|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fundrising|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fundrising[]    findAll()
 * @method Fundrising[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FundrisingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fundrising::class);
    }

    public function add(Fundrising $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Fundrising $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function orderByDateASC()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.date_Don', 'ASC')
            ->getQuery()->getResult();
    }


    public function findrecByfundsTitle($title){
        return $this->createQueryBuilder('Fundrising')
            ->where('Fundrising.TitreDon LIKE :TitreDon')
            ->setParameter('TitreDon', '%'.$title.'%')
            ->getQuery()
            ->getResult();
    }


    public  function sms(){
        // Your Account SID and Auth Token from twilio.com/console
                $sid = 'AC97c73f447d8b48e0816226228f7951f4';
                $auth_token = '848f71a92c52a845e720a41fb80c796d';
        // In production, these should be environment variables. E.g.:
        // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]
        // A Twilio number you own with SMS capabilities
                $twilio_number = "+12762624016";
        
                $client = new Client($sid, $auth_token);
                $client->messages->create(
                // the number you'd like to send the message to
                    '+21698428606',
                    [
                        // A Twilio phone number you purchased at twilio.com/console
                        'from' => '+12762624016',
                        // the body of the text message you'd like to send
                        'body' => 'New fund has been raised'
                    ]
                );
            }
       
     
       
//    /**
//     * @return Fundrising[] Returns an array of Fundrising objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Fundrising
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
