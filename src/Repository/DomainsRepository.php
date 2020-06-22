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
    
    public function getDomainsToCheck() {
        return $this->createQueryBuilder ('d')
            ->andWhere ('d.is_owned = 0')
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
