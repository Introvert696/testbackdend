<?php
namespace App\Tests\Services\AdaptersService;

use App\Entity\Patients;
use App\Tests\Services\BaseService;

class PatientToPatientResponseDTOTest extends BaseService
{
    public function testMain(): void
    {
        $patient = new Patients();
        $patient->setName("Test");
        $patient->setCardNumber(333);

        $patientResponse = $this->adapterService->patientToPatientResponseDTO($patient);

        $this->assertObjectHasProperty('name',$patientResponse);
        $this->assertObjectHasProperty('card_number',$patientResponse);
        $this->assertObjectHasProperty('chamber',$patientResponse);
        $this->assertObjectHasProperty('procedures',$patientResponse);
    }

}