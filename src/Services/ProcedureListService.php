<?php

namespace App\Services;

use App\DTO\ProcListDTO;
use App\Repository\ProceduresRepository;

class ProcedureListService
{
    public function __construct(
        private readonly ProceduresRepository $proceduresRepository,
    ){}
    public function validate(ProcListDTO $pc): ProcListDTO|null
    {
        $procedure = $this->proceduresRepository->find($pc->getProcedureId());
        if(($pc->getProcedureId()!=null)and($pc->getQueue()!=null)and($pc->getStatus()!=null)and($procedure!==null))
            return $pc;
        else
            return null;

    }

}