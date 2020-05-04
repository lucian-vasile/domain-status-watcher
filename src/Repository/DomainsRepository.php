<?php

namespace App\Repository;

use App\Entity\Domains;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Domains|null find($id, $lockMode = null, $lockVersion = null)
 * @method Domains|null findOneBy(array $criteria, array $orderBy = null)
 * @method Domains[]    findAll()
 * @method Domains[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Domains::class);
    }

    // /**
    //  * @return Domains[] Returns an array of Domains objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Domains
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
    public function getDomainsToCheck() {
        return $this->createQueryBuilder ('d')
            ->andWhere ('d.checked_at < :ten_minutes_ago or d.checked_at IS NULL')
            ->andWhere ('d.is_owned = 0')
            ->andWhere ('d.expires_at <= :now or d.expires_at IS NULL')
            ->setParameter ('ten_minutes_ago', new \DateTime('-10 minutes'),Types::DATE_MUTABLE)
            ->setParameter ('now', new \DateTime(), Types::DATE_MUTABLE)
            ->getQuery ()
            ->getResult ();
    }
    
    public function findOneByIdOrDomain($idOrDomain) {
        return $this->createQueryBuilder ('d')
            ->orWhere ('d.id = :idOrDomain')
            ->orWhere ('d.domain = :idOrDomain')
            ->setParameter ('idOrDomain', $idOrDomain)
            ->getQuery ()
            ->getOneOrNullResult ();
    }
}
