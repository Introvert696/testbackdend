<?php

namespace App\Services;

use App\Entity\ChambersPatients;
use App\Repository\ChambersPatientsRepository;
use App\Repository\ChambersRepository;

class ChambersPatientsService
{
    public function __construct(
        private readonly ChambersPatientsRepository $chambersPatientsRepository,
    )
    {
    }

    public function create($patient,$chamber): object
    {
        $chamberPatients = new ChambersPatients();
        $chamberPatients->setPatients($patient);
        $chamberPatients->setChambers($chamber);
        return $chamberPatients;
    }
    public function getByPatientId($id): array
    {
        $chambersPatient = $this->chambersPatientsRepository->findBy(["patients_id"=>$id]);

    }

}