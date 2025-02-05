<?php

namespace App\Services;

use App\DTO\PatientDTO;
use App\DTO\PatientResponseDTO;
use App\Entity\Patients;
use App\Repository\ChambersRepository;
use App\Repository\PatientsRepository;
use App\Repository\ProcedureListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PatientsServices
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $em,
        private readonly JsonResponseHelper $responseHelper,
        private readonly PatientsRepository $patientsRepository,
        private readonly ChambersRepository $chambersRepository,
        private readonly ProcedureListRepository $procedureListRepository,
        private readonly ChambersPatientsService $chambersPatientsService,
    )
    {
    }
    public function all(): array
    {
        return $this->responseHelper->generate('Ok',200,'return all patient',$this->patientsRepository->findAll());
    }
    public function createOrFind($data):array
    {
        $data = $this->checkData($data,'App\Entity\Patients','Patients');
        if(!$data){
            return $this->responseHelper->generate('Error',502,'Check fields');
        }
        if($this->validatePatient($data)===null){
            return $this->responseHelper->generate('Error',502,'Check all fields');
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
        $updatedData = $this->checkData($data,'App\DTO\PatientDTO');
        $patient = $this->patientsRepository->find($id);
        if(!$updatedData){
            return $this->responseHelper->generate('Error',402,'Field not filled');
        }
        $chamber = $updatedData->chamber!=null?$this->chambersRepository->find($updatedData->chamber):null;
        if(!$patient){
            return $this->responseHelper->generate('Not Found',404,'Patient not found');
        }
        if($updatedData->name){
            $patient->setName($updatedData->name);
        }
        if (!$chamber) {
            return $this->responseHelper->generate('Not found',404,'Chamber Not Found');
        }
        $chamberPatients = $patient->getChambersPatients();
        if ($chamberPatients) {
            $chamberPatients->setChambers($chamber);
        } else {
            $chamberPatients = $this->chambersPatientsService->create($patient,$chamber);
            $this->em->persist($chamberPatients);
        }
        $this->em->flush();

        return $this->responseHelper->generate('Ok',200,'Patient has been updated',$this->adapterPatientToPatientResponseDTO($patient) );
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

        return $this->responseHelper->generate('Ok',200,"Patient has been delete");;
    }
    public function about(int $id): array
    {
        // протестить эту функцию
        $patient = $this->patientsRepository->find($id);
        $procList = $this->procedureListRepository->findBy(['source_type'=>'patients','source_id'=>$id]);
        $data['patient'] = $patient;
        $data['procedure_list'] = $procList;

        return $this->responseHelper->generate('Ok',200,"Patient info",$data);
    }
    public function checkData($data,$classname): null|object
    {
        if(!class_exists($classname)){
            return null;
        }
        try{
            $data = $this->serializer->deserialize(data: $data,type: $classname,format:'json');
        }
        catch (\Exception){
            return null;
        }
        return $data;
    }
    public function validatePatient(Patients $data): null|Patients
    {
        if(($data->getName()!==null) and ($data->getCardNumber()!== null)){
            return $data;
        }
        else{
            return null;
        }
    }
    public function adapterPatientToPatientResponseDTO(Patients $patients): PatientResponseDTO
    {
        $patientResponse = new PatientResponseDTO();
        $patientResponse->setId($patients->getId());
        $patientResponse->setName($patients->getName());
        $patientResponse->setCardNumber($patients->getCardNumber());
        $patientResponse->setChamber($patients->getChambersPatients()->getChambers()->getId());

        return $patientResponse;
    }

}