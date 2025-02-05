<?php

namespace App\Services;

use App\DTO\ProcListDTO;
use App\Entity\ProcedureList;
use App\Entity\Procedures;
use App\Repository\ProceduresRepository;

class ProcedureListService
{
    public function __construct(
        private readonly ProceduresRepository $proceduresRepository,
    )
    {
    }

    public function createObject(Procedures $proc,int $queue,int $sourceId,bool $status=false) :ProcedureList
    {
        $procedureList = new ProcedureList();
        $procedureList->setProcedures($proc);
        $procedureList->setQueue($queue);
        $procedureList->setSourceType("chambers");
        $procedureList->setSourceId($sourceId);
        $procedureList->setStatus(false);
        return $procedureList;
    }
    public function validate(ProcListDTO $pc): ProcListDTO|null
    {
        $procedure = $this->proceduresRepository->find($pc->getProcedureId());

        if(($pc->getProcedureId()!=null)and($pc->getQueue()!=null)and($pc->getStatus()!=null)and($procedure!==null))
            return $pc;
        else
            return null;

    }
    public function procListDtoToProcList(ProcListDTO $procList,$id): ProcedureList
    {
        $procedure = $this->proceduresRepository->find($procList->getProcedureId());
        $procedureList = new ProcedureList();
        $procedureList->setSourceType('chambers');
        $procedureList->setSourceId($id);
        $procedureList->setProcedures($procedure);
        $procedureList->setQueue($procList->queue);
        $procedureList->setStatus($procList->status);

        return $procedureList;
    }
}