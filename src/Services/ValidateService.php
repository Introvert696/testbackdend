<?php
namespace App\Services;

use App\DTO\Chamber\ProcListDTO;
use App\Entity\ProcedureList;
use App\Repository\ProceduresRepository;

class ValidateService
{
    public function __construct(
        private readonly ProceduresRepository $proceduresRepository,
    ){}
    public function chambersRequestData(object|null $data): object|null
    {
        if($data->getNumber()!==null)
            return $data;
        return null;
    }
    public function procedureList(ProcedureList $pl): ProcedureList|null
    {
        $res =  (($pl->getProcedures()!==null) and
                ($pl->getStatus()!==null) and
                ($pl->getQueue()!==null) and
                ($pl->getId()));
        if($res){
            return $pl;
        } else {
            return null;
        }
    }
    public function procListDTO(ProcListDTO $pld): ProcListDTO|null
    {
        $res = (($pld->procedure_id!==null) and
                ($pld->queue!==null) and
                ($pld->status!==null));
        if($res){
            return $pld;
        } else {
            return null;
        }
    }
    public function patients(object $patient): object|false
    {
        $res = (($patient->getName()!==null) and
                ($patient->getCardNumber()!== null));
        if($res)
        {
            return $patient;
        } else {
            return false;
        }
    }
    public function procedureListWithProcedure(ProcListDTO $pc): ProcListDTO|bool
    {

        $procedure = $this->proceduresRepository->find($pc->getProcedureId());

        if(!$procedure){
            return false;
        }
        $res = (($pc->getProcedureId()!==null) and
                ($pc->getQueue()!==null) and
                ($pc->getStatus()!==null)
        );
        if($res){

            return $pc;
        } else {
            return false;
        }
    }
    public function procedures(object $proc): object|bool
    {
        $res = (($proc->getTitle()!==null) and
                ($proc->getDescription()!==null));
        if($res){
            return $proc;
        } else {
            return false;
        }
    }
    public function validate(mixed... $data): array|false
    {
        $result = [];

        for($i=0;$i<count($data);$i++){
            if(!$data[$i]){
                $result[$i] = false;
            }
            else{
                $result[$i] = $data[$i];
            }
        }
        return $result;
    }
}