<?php
namespace App\Services;

use App\DTO\ProcListDTO;
use App\Entity\ProcedureList;
use App\Entity\Procedures;
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
    public function patients(object $data): object|null
    {
        $res = (($data->getName()!==null) and
                ($data->getCardNumber()!== null));
        if($res)
        {
            return $data;
        } else {
            return null;
        }
    }
    public function procedureListWithProcedure(ProcListDTO $pc): ProcListDTO|null
    {
        if($pc->getProcedureId()){
            $procedure = $this->proceduresRepository->find(
                $pc->getProcedureId()
            );
        } else {
            return null;
        }
        $res = (($pc->getProcedureId()!==null) and
                ($pc->getQueue()!==null) and
                ($pc->getStatus()!==null) and
                ($procedure!==null));
        if($res){
            return $pc;
        } else {
            return null;
        }
    }
    public function procedures(Procedures|null $data): Procedures|null
    {
        $res = (($data !== null) and
                ($data->getTitle()!==null) and
                ($data->getDescription()!==null));
        if($res){
            return $data;
        } else {
            return null;
        }
    }
    public function validate(mixed $data): array|null
    {
        $result = [];
        for($i=0;$i<count($data);$i++){
            if(!$data[$i]){
                $result[$i] = null;
            }
            else{
                $result[$i] = $data[$i];
            }
        }
        return $result;
    }
}