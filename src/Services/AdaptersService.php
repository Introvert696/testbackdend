<?php

namespace App\Services;

use App\DTO\ChamberProcedureDTO;
use App\DTO\PatientResponseDTO;
use App\DTO\ProcedureResponseDTO;
use App\DTO\ProcListDTO;
use App\DTO\ProcListRespDTO;
use App\Entity\Patients;
use App\Entity\ProcedureList;
use App\Entity\Procedures;
use App\Repository\ProceduresRepository;

class AdaptersService
{


    public function __construct(
        private readonly ProceduresRepository $proceduresRepository,
        private readonly ValidateService $validator,
    ){}
    public function patientToPatientResponseDTO(
        Patients $patients,
        $procList = null
    ): PatientResponseDTO| null
    {
        $patients = $this->validator->patients($patients);
        if(!$patients){
            return null;
        }
        $patientResponse = new PatientResponseDTO();
        if($patients->getId()){
            $patientResponse->setId($patients->getId());
        }
        $patientResponse->setName($patients->getName());
        $patientResponse->setCardNumber($patients->getCardNumber());

        if ($patients->getChambersPatients()){
            $patientResponse->setChamber(
                $patients->getChambersPatients()->getChambers()->getId()
            );
        }
        if ($procList){
            foreach ($procList as $pl){
                $patientResponse->addProc(
                    $this->procedureListToChamberProcedureDto($pl)
                );
            }
        }

        return $patientResponse;
    }
    public function procedureListToChamberProcedureDto(
        ProcedureList $procList
    ): ChamberProcedureDTO|null
    {
        $pl = $this->validator->procedureList($procList);
        if(!$pl){
            return null;
        }
        $procListDTO = new ChamberProcedureDTO();
        $procListDTO->setId($procList->getProcedures()->getId());
        $procListDTO->setStatus($procList->getStatus());
        $procListDTO->setQueue($procList->getQueue());
        $procListDTO->setTitle($procList->getProcedures()->getTitle());
        $procListDTO->setDesc($procList->getProcedures()->getDescription());

        return $procListDTO;
    }
    public function procListDtoToProcList(
        ProcListDTO $procList,
        $id
    ): ProcedureList|null
    {
        $procList = $this->validator->procListDTO($procList);
        if(!$procList){
            return null;
        }
        $procedure = $this->proceduresRepository->find($procList->getProcedureId());
        $procedureList = new ProcedureList();
        $procedureList->setSourceType('chambers');
        $procedureList->setSourceId($id);
        $procedureList->setProcedures($procedure);
        $procedureList->setQueue($procList->queue);
        $procedureList->setStatus($procList->status);

        return $procedureList;
    }
    public function procedureToProcedureResponseDTO(
        Procedures $procedures
    ): ProcedureResponseDTO|null
    {
        $newProcResponse = new ProcedureResponseDTO();
        $procedures= $this->validator->procedures($procedures);
        if(!$procedures){
            return null;
        }
        if($procedures->getId()){
            $newProcResponse->setId($procedures->getId());
        }
        $newProcResponse->setTitle($procedures->getTitle());
        $newProcResponse->setDescription($procedures->getDescription());

        return $newProcResponse;
    }
    public function procListToProcListRespDTO(
        ProcedureList $procList
    ): ProcListRespDTO|null
    {
        $procList = $this->validator->procedureList($procList);
        if(!$procList){
            return null;
        }
        $procListResp = new ProcListRespDTO();
        $procListResp->setStatus($procList->getStatus());
        $procListResp->setQueue($procList->getQueue());
        $procListResp->setSourceId($procList->getSourceId());
        $procListResp->setSourceType($procList->getSourceType());

        return $procListResp;
    }

}

