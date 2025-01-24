<?php

namespace App\Services;

use App\Entity\ChambersPatients;
use App\Entity\Patients;
use App\Repository\ChambersRepository;
use App\Repository\PatientsRepository;
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
        $result = $this->patientsRepository->findByCardNumber($patient->getCardNumber());

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
        $respData['status']=200;

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
//            $respData['message'] .= ' Patient has been updated';

        }

        return $this->responseHelper->generate('Ok',200,$respData['message'],Array($patient));
    }
}