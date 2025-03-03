<?php

namespace App\Services;

use App\DTO\Adapter\ChamberProcedureDTO;
use App\DTO\Adapter\PatientResponseDTO;
use App\DTO\Adapter\ProcedureResponseDTO;
use App\DTO\Adapter\ProcedureListResponseDTO;
use App\DTO\Chamber\ProcedureListDTO;
use App\Entity\Patients;
use App\Entity\ProcedureList;
use App\Entity\Procedures;
use App\Repository\ProceduresRepository;

class AdaptersService
{
    public function __construct(
        private readonly ProceduresRepository $proceduresRepository,
        private readonly ValidateService      $validator,
    )
    {
    }

    public function convertPatientToPatientResponseDTO(
        object $patients,
               $procList = null
    ): PatientResponseDTO|bool
    {
        $patients = $this->validator->validatePatients($patients);
        if (!$patients) {
            return false;
        }
        $patientResponse = new PatientResponseDTO();
        if ($patients->getId()) {
            $patientResponse->setId($patients->getId());
        }
        $patientResponse->setName($patients->getName());
        $patientResponse->setCardNumber($patients->getCardNumber());

        if ($patients->getChambersPatients()) {
            $patientResponse->setChamber(
                $patients->getChambersPatients()->getChambers()->getId()
            );
        }
        if ($procList) {
            foreach ($procList as $pl) {
                $patientResponse->addProc(
                    $this->convertProcedureListToChamberProcedureDto($pl)
                );
            }
        }

        return $patientResponse;
    }

    public function convertProcedureListToChamberProcedureDto(
        ProcedureList $procList
    ): ChamberProcedureDTO|false
    {
        $pl = $this->validator->validateProcedureList($procList);
        if (!$pl) {
            return false;
        }
        $procListDTO = new ChamberProcedureDTO();
        $procListDTO->setId($procList->getProcedures()->getId());
        $procListDTO->setStatus($procList->getStatus());
        $procListDTO->setQueue($procList->getQueue());
        $procListDTO->setTitle($procList->getProcedures()->getTitle());
        $procListDTO->setDesc($procList->getProcedures()->getDescription());

        return $procListDTO;
    }

    public function convertProcedureListDtoToProcedureList(
        ProcedureListDTO $procList,
                         $id
    ): ProcedureList|false
    {
        $procList = $this->validator->validateProcedureListDTO($procList);
        if (!$procList) {
            return false;
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

    public function convertProcedureToProcedureResponseDTO(
        Procedures $procedures
    ): ProcedureResponseDTO|false
    {
        $newProcResponse = new ProcedureResponseDTO();
        $procedures = $this->validator->validateProcedures($procedures);
        if (!$procedures) {
            return false;
        }
        if ($procedures->getId()) {
            $newProcResponse->setId($procedures->getId());
        }
        $newProcResponse->setTitle($procedures->getTitle());
        $newProcResponse->setDescription($procedures->getDescription());

        return $newProcResponse;
    }

    public function convertProcedureListToProcedureListResponseDTO(
        ProcedureList $procList
    ): ProcedureListResponseDTO|false
    {
        $procList = $this->validator->validateProcedureList($procList);
        if (!$procList) {
            return false;
        }
        $procListResp = new ProcedureListResponseDTO();
        $procListResp->setStatus($procList->getStatus());
        $procListResp->setQueue($procList->getQueue());
        $procListResp->setSourceId($procList->getSourceId());
        $procListResp->setSourceType($procList->getSourceType());

        return $procListResp;
    }

}

