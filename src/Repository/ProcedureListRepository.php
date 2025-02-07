<?php

namespace App\Repository;

use App\Entity\ProcedureList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProcedureList>
 */
class ProcedureListRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    )
    {

        parent::__construct($registry, ProcedureList::class);
    }
}
