<?php

namespace App\Repository;

use App\Entity\Chambers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chambers>
 */
class ChambersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chambers::class);
    }
    public function findByNumber($number):Chambers|null
    {

        $result = $this->createQueryBuilder('c')
            ->andWhere('c.number = :number')
            ->setParameter('number' ,$number)
            ->getQuery()
            ->getResult();

        if(!$result){
            return null;
        }
        else{
            return $result[0];
        }

    }

}
