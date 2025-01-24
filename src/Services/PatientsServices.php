<?php

namespace App\Services;

use App\Entity\Patients;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PatientsServices
{
    public function __construct(
        private readonly  SerializerInterface $serializer,
        private readonly EntityManagerInterface $em
    )
    {
    }
public function createOrFind($data):array {
    $patient = $this->serializer->deserialize(data: $data,type: Patients::class,format:'json');
    $patientRepository = $this->em->getRepository(Patients::class);
    $result = $patientRepository->findByCardNumber($patient->getCardNumber());

    if(!$result){
        $this->em->persist($patient);
        $this->em->flush();
        $response['message'] = 'patient created';
        $response['patient'][] = $patient;
    }
    else{
        $response['message'] = 'Create error, found patient';
        $response['patient'] = $result;
    }

    return $response;
}
}