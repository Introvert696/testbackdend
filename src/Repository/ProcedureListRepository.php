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
        private readonly EntityManagerInterface $em,
    )
    {

        parent::__construct($registry, ProcedureList::class);
    }
    public function findByProcedureQueuePatient(int $procedureId,int $queue,int $patientId):array
    {
        $result = $this->em->createQuery('
            SELECT pl.id, pl.procedures, pl.queue
            FROM App\Entity\ProcedureList pl
');
        dd($result->getResult());
        return [];
    }
}
