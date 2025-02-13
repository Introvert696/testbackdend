<?php

namespace App\Services;

use App\Repository\ChambersRepository;
use App\Repository\PatientsRepository;
use App\Repository\ProcedureListRepository;
use Doctrine\ORM\EntityManagerInterface;

class PatientsServices
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly JsonResponseHelper $responseHelper,
        private readonly PatientsRepository $patientsRepository,
        private readonly ChambersRepository $chambersRepository,
        private readonly ProcedureListRepository $procedureListRepository,
        private readonly ChambersPatientsService $chambersPatientsService,
        private readonly AdaptersService $adaptersService,
        private readonly JsonResponseHelper $jsonResponseHelper,
    )
    {
    }
    public function all(): array
    {
        return $this->responseHelper->generate('Ok',200,'return all patient',$this->patientsRepository->findAll());
    }
    public function createOrFind($data):array
    {
        $data = $this->jsonResponseHelper->checkData($data,'App\Entity\Patients');
        if(!$data){
            return $this->responseHelper->generate('Error',400,'Check fields');
        }
        if($this->validatePatient($data)===null){
            return $this->responseHelper->generate('Error',400,'Check all fields');
        }
        $result = $this->patientsRepository->findBy(['card_number'=>$data->getCardNumber()]);
        if($result){
            return $this->responseHelper->generate('Conflict',409,'card_number already exists');
        }
        $this->em->persist($data);
        $this->em->flush();

        return $this->responseHelper->generate('Created',200,'patient has been created',$data);
    }
    public function update($id,$data): array
    {
        $updatedData = $this->jsonResponseHelper->checkData($data,'App\DTO\PatientDTO');
        $patient = $this->patientsRepository->find($id);
        if(!$updatedData){
            return $this->responseHelper->generate('Error',402,'Field not filled');
        }
        $chamber = $updatedData->chamber!=null?$this->chambersRepository->find($updatedData->chamber):null;
        // совместить
        if(!$patient){
            return $this->responseHelper->generate('Not Found',404,'Patient not found');
        }
        if($updatedData->name){
            $patient->setName($updatedData->name);
        }
        // совместить
        if (!$chamber) {
            return $this->responseHelper->generate('Not found',422,'Chamber not found');
        }
        $chamberPatients = $patient->getChambersPatients();
        if ($chamberPatients) {
            $chamberPatients->setChambers($chamber);
        } else {
            $chamberPatients = $this->chambersPatientsService->create($patient,$chamber);
            $this->em->persist($chamberPatients);
        }
        $this->em->flush();

        return $this->responseHelper->generate('Ok',200,'Patient has been updated',$this->adaptersService->patientToPatientResponseDTO($patient) );
    }
    public function remove($id): array
    {
        $patient = $this->patientsRepository->getMore($id);
        if(!$patient){
            return $this->responseHelper->generate('Not Found',404,"Patient not found");
        }
        $procedureList = $this->procedureListRepository->findBy([
            'source_type' => 'patients',
            'source_id' => $id
        ]);
        foreach ($procedureList as $pl){
            $this->em->remove($pl);
        }
        $this->em->remove($this->responseHelper->first($patient));
        $this->em->flush();

        return $this->responseHelper->generate('Ok',200,"Patient has been delete");
    }
    public function about(int $id): array
    {
        $patient = $this->patientsRepository->find($id);
        if(!$patient){
            return $this->responseHelper->generate('Not found',404,"Patient not found");
        }
        $procList = $this->procedureListRepository->findBy(['source_type'=>'patients','source_id'=>$id]);
        $patient = $this->adaptersService->patientToPatientResponseDTO($patient,$procList);

        return $this->responseHelper->generate('Ok',200,"Patient info",$patient);
    }
    public function validatePatient(object $data): null|object
    {
        if(($data->getName()!==null) and ($data->getCardNumber()!== null)){
            return $data;
        }
        else{
            return null;
        }
    }


}