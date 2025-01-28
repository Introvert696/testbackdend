<?php

namespace App\Repository;

use App\Entity\Patients;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Patients>
 */
class PatientsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {

        parent::__construct($registry, Patients::class);
    }

    public function findAll() : array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id','p.name','p.card_number')
            ->getQuery()
            ->getResult();
    }

    public function get(int $id): array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'c', 'cp')  // Выбираем только нужные столбцы
            ->leftJoin('p.chambersPatients', 'cp')  // LEFT JOIN с ChambersPatients
            ->leftJoin('cp.chambers', 'c')
            ->where('p.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getResult();
    }

}
