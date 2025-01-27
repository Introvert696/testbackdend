<?php

namespace App\Repository;

use App\Entity\ChambersPatients;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChambersPatients>
 */
class ChambersPatientsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {

        parent::__construct($registry, ChambersPatients::class);
    }


}
