<?php

namespace App\Services;

use App\Entity\ChambersPatients;
use App\Entity\Patients;
use App\Entity\ProcedureList;
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
    )
    {
    }
    public function all(): array
    {
        $patients = $this->patientsRepository->findAll();

        return $this->responseHelper->generate('Ok',200,'Return all patients',$patients);
    }
    public function createOrFind($data):array {
        //мб сделать еще одну сущность что бы обрабатывать запрос, потоому что вложенные данные неоч
        // для этих целей лучше использовать DTO
        // в понедельник попробывать сделать все через DTO
        $patient = $this->serializer->deserialize(data: $data,type: Patients::class,format:'json');
        $result = $this->patientsRepository->findBy(['card_number'=>$patient->getCardNumber()]);

        if(!$result){
            $this->em->persist($patient);
            $this->em->flush();
            $response = $this->responseHelper->generate('Created',202,'Patient has been created',Array($patient));
        }
        else{
            $response = $this->responseHelper->generate('Not Created',304,'Failed to create, patient exists',$result);
        }

        return $response;
    }
    public function update($id,$data): array{
        $respData['message']="Update,";

        $updatedData = $this->serializer->deserialize(data: $data,type: Patients::class,format:'json');
        $patient = $this->patientsRepository->find($id);

        if(!$patient){
            return $this->responseHelper->generate('Not Found',404,'User not found');
        }
        else{
            $updatedData->getName()? $patient->setName($updatedData->getName()):'';
            if ($updatedData->getChambersPatients() and ($updatedData->getChambersPatients()->getChambers())){
                $patientInChamber = $patient->getChambersPatients();

                if(!$patientInChamber or (!$updatedData->getChambersPatients()->getChambers()->getNumber())){
                    $patientInChamber = new ChambersPatients();
                    $patientInChamber->setPatients($patient);
                    $patientInChamber->setChambers($this->chambersRepository->findByNumber($updatedData->getChambersPatients()->getChambers()->getNumber()));
                    $this->em->persist($patientInChamber);
                    $respData['message'] .= ' Patient';
                }
                else{
                    if($this->chambersRepository->findByNumber($updatedData->getChambersPatients()->getChambers()->getNumber())){
                        $newChamber = $this->chambersRepository->findByNumber($updatedData->getChambersPatients()->getChambers()->getNumber());
                        $patient->getChambersPatients()->setChambers($newChamber);
                        $respData['message'] .= ' Chamber';
                    }
                    else{
                        $respData['message'] .= ' Chamber not found';
                    }

                }

                $this->em->flush();
            }

        }

        return $this->responseHelper->generate('Ok',200,$respData['message'],Array($patient));
    }
    public function remove($id): array
    {
        $patient = $this->patientsRepository->get($id);
        if(!$patient){
            $response = $this->responseHelper->generate('Not Found',404,"Patient not found");
        }
        else{
            foreach ($patient as $p){
                $this->em->remove($p);
            }
            $this->em->flush();
            $response = $this->responseHelper->generate('Ok',200,"Patient has been removed");
        }

        return $response;
    }
    public function about(int $id): array
    {
        $patient = $this->patientsRepository->find($id);
        $procList = $this->procedureListRepository->findBy(['source_type'=>'patients','source_id'=>$id]);
        $data['patient'] = $patient;
        $data['procedure_list'] = $procList;
        $response = $this->responseHelper->generate('Ok',200,"Patient info",$data);
        return $response;
    }
}