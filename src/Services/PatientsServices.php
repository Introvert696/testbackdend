<?php

namespace App\Services;

use App\Repository\ChambersRepository;
use App\Repository\PatientsRepository;
use App\Repository\ProcedureListRepository;
use Doctrine\ORM\EntityManagerInterface;
class PatientsServices
{
    public function __construct(
        private readonly EntityManagerInterface  $em,
        private readonly ResponseHelper          $responseHelper,
        private readonly PatientsRepository      $patientsRepository,
        private readonly ChambersRepository      $chambersRepository,
        private readonly ProcedureListRepository $procedureListRepository,
        private readonly ChambersPatientsService $chambersPatientsService,
        private readonly AdaptersService         $adaptersService,
        private readonly ResponseHelper          $jsonResponseHelper,
        private readonly ValidateService         $validator,
    ){}
    public function all(): array
    {
        return $this->responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            'return all patient',
            $this->patientsRepository->findAll());
    }
    public function store($data):array
    {
        $data = $this->jsonResponseHelper
            ->checkData($data,'App\Entity\Patients');
        $result = $this->patientsRepository->findBy([
            'card_number'=>$data?->getCardNumber()
        ]);
        if(!$data or !$this->validator->patients($data) or $result){
            return $this->responseHelper->generate(
                'Error',
                ResponseHelper::STATUS_NOT_VALID_BODY,
                'Check body');
        }
        $this->em->persist($data);
        $this->em->flush();

        return $this->responseHelper->generate(
            'Created',
            ResponseHelper::STATUS_OK,
            'patient has been created',
            $data);
    }
    public function update($id,$data): array
    {
        $updatedData = $this->jsonResponseHelper
            ->checkData($data,'App\DTO\PatientDTO');
        $patient = $this->patientsRepository->find($id);
        $chamber = $updatedData->chamber!=null?
            $this->chambersRepository->find($updatedData?->chamber):null;
        if(!$updatedData ){
            return $this->responseHelper->generate(
                'Error',
                ResponseHelper::STATUS_NOT_VALID_FIELDS,
               'Check you field');
        }
        if(!$patient){
            return $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Patient - not found');
        }
        if (!$chamber){
            return $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Patient - not found');
        }
        $patient->setName($updatedData?->name ?? $patient->getName());
        $chamberPatients = $patient->getChambersPatients();
        if ($chamberPatients){
            $chamberPatients->setChambers($chamber);
        } else{
            $chamberPatients = $this->chambersPatientsService->create(
                $patient,
                $chamber
            );
            $this->em->persist($chamberPatients);
        }
        $this->em->flush();

        return $this->responseHelper->generate(
            'Updated',
            ResponseHelper::STATUS_OK,
            'Patient has been updated',
            $this->adaptersService->patientToPatientResponseDTO($patient));
    }
    public function delete($id): array
    {
        $patient = $this->patientsRepository->getMore($id);
        $procedureList = $this->procedureListRepository->findBy([
            'source_type' => 'patients',
            'source_id' => $id
        ]);

        if(!$patient){
            return $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                "Patient not found");
        }
        foreach ($procedureList as $pl){
            $this->em->remove($pl);
        }
        $this->em->remove($this->responseHelper->first($patient));
        $this->em->flush();

        return $this->responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            "Patient has been delete");
    }
    public function about(int $id): array
    {
        $patient = $this->patientsRepository->find($id);
        $procList = $this->procedureListRepository->findBy([
            'source_type'=>'patients',
            'source_id'=>$id
        ]);
        if(!$patient){
            return $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                "Patient not found");
        }
        $patient = $this->adaptersService->patientToPatientResponseDTO(
            $patient,
            $procList
        );

        return $this->responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            "Patient info",
            $patient);
    }
}