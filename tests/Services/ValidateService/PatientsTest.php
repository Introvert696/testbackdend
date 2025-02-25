<?php
namespace App\Tests\Services\ValidateService;

use App\Entity\Patients;
use App\Repository\PatientsRepository;
use App\Tests\Services\BaseService;

class PatientsTest extends BaseService
{
    public function testNotValid(): void
    {
        $patients = new Patients();
        $result = $this->validateService->patients($patients);
        $this->assertFalse($result);
    }

    public function testValid(): void
    {
        $patient = $this->patientRepository->find(4);
        $result = $this->validateService->patients($patient);
        $this->assertNotNull($result);
    }
}