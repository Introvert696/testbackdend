<?php
namespace App\Services;

use App\DTO\ProcListDTO;
use App\Entity\ProcedureList;
use App\Repository\ProceduresRepository;

class ValidateService
{
    public function __construct(
        private readonly ProceduresRepository $proceduresRepository,
    ){}
    public function chambersRequestData(object $data): object|null
    {
        if($data->getNumber()!==null)
            return $data;
        return null;
    }
    public function procedureList(ProcedureList $pl): ProcedureList|null
    {
        if(($pl->getProcedures()!==null) and($pl->getStatus()!==null) and ($pl->getQueue()!==null) and ($pl->getId())){
            return $pl;
        }
        else{
            return null;
        }
    }
    public function procListDTO(ProcListDTO $pld): ProcListDTO|null
    {
        if(($pld->procedure_id!==null) and ($pld->queue!==null) and ($pld->status!==null)){
            return $pld;
        }
        else{
            return null;
        }
    }
    public function patients(object $data): object|null
    {
        if(($data->getName()!==null) and ($data->getCardNumber()!== null)){
            return $data;
        }
        else{
            return null;
        }
    }
    public function procedureListWithProcedure(ProcListDTO $pc): ProcListDTO|null
    {
        $procedure = $this->proceduresRepository->find($pc->getProcedureId());
        if(($pc->getProcedureId()!==null)and($pc->getQueue()!==null)and($pc->getStatus()!==null)and($procedure!==null))
            return $pc;
        else
            return null;
    }
}