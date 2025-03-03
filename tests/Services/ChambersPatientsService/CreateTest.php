<?php

namespace App\Tests\Services\ChambersPatientsService;

use App\Entity\Chambers;
use App\Entity\Patients;
use App\Tests\Services\BaseService;

class CreateTest extends BaseService
{

    public function testMain(): void
    {
        $test = $this->chamberPatients->createChamberPatients(new Patients(), new Chambers());

        $this->assertObjectHasProperty('chambers', $test);
        $this->assertObjectHasProperty('patients', $test);
    }
}