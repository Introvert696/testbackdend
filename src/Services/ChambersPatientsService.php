<?php

namespace App\Services;

use App\Entity\ChambersPatients;

class ChambersPatientsService
{
    public function create($patient,$chamber): object
    {
        $chamberPatients = new ChambersPatients();
        $chamberPatients->setPatients($patient);
        $chamberPatients->setChambers($chamber);
        return $chamberPatients;
    }

}