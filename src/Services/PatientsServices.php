<?php

namespace App\Services;

use App\DTO\PatientDTO;
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
        $patient = $this->serializer->deserialize(data: $data,type: Patients::class,format:'json');
        $result = $this->patientsRepository->findBy(['card_number'=>$patient->getCardNumber()]);

        if(!$result){
            $this->em->persist($patient);
            $this->em->flush();
            $response = $this->responseHelper->generate('Created',200,'patient has been created',Array($patient));
        }
        else{
            $response = $this->responseHelper->generate('Conflict',409,'card_number already exists');
        }

        return $response;
    }
    public function update($id,$data): array
    {
        $updatedData = $this->serializer->deserialize(data: $data,type: PatientDTO::class,format:'json');
        $patient = $this->patientsRepository->find($id);
        $chamber = $updatedData->chamber!=null?$this->chambersRepository->find($updatedData->chamber):null;
        if(!$patient){
            return $this->responseHelper->generate('Not Found',404,'Patient not found');
        }
        else {
            if($updatedData->name){
                $patient->setName($updatedData->name);
            }
            if ($chamber) {
                $chamberPatients = $patient->getChambersPatients();
                if ($chamberPatients) {
                    $chamberPatients->setChambers($chamber);
                } else {
                    $chamberPatients = $this->chambersPatientsService->create($patient,$chamber);
                    $this->em->persist($chamberPatients);
                }
            }
            $this->em->flush();
        }

        return $this->responseHelper->generate('Ok',200,'Patient has been updated',Array($patient));
    }
    public function remove($id): array
    {
        $patient = $this->patientsRepository->getMore($id);
        if(!$patient){
            $response = $this->responseHelper->generate('Not Found',404,"Patient not found");
        }
        else{
            foreach ($patient as $p){
                $this->em->remove($p);
            }
            $this->em->flush();
            $response = $this->responseHelper->generate('Ok',200,"Patient has been delete");
        }

        return $response;
    }
    public function about(int $id): array
    {
        // test it
        $patient = $this->patientsRepository->find($id);
        $procList = $this->procedureListRepository->findBy(['source_type'=>'patients','source_id'=>$id]);
        $data['patient'] = $patient;
        $data['procedure_list'] = $procList;
        return $this->responseHelper->generate('Ok',200,"Patient info",$data);
    }
}