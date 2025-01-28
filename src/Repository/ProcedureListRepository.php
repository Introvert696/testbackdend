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
    public function findByProcedureQueuePatient(int $id, int $procedureId,int $queue,int $patientId):array
    {
        $query = $this->em->createQuery('
            SELECT pl.id,pl.queue,identity(pl.procedures) procedures, identity(pl.patients) patients,pl.status
            FROM App\Entity\ProcedureList pl
            where pl.procedures = :procid
            and pl.queue = :que
            and pl.patients = :patid
            and pl.id = :id')
        ->setParameter("procid",$procedureId)
        ->setParameter('que',$queue)
        ->setParameter('patid',$patientId)
        ->setParameter('id',$id);
        $result = $query->getResult();
        return $result;
    }
}
