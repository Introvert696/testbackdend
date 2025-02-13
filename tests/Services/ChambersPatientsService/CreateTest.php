<?php

namespace App\Tests\Services\ChambersPatientsService;

use App\Entity\Chambers;
use App\Entity\Patients;
use App\Services\ChambersPatientsService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateTest extends KernelTestCase
{
    private $container;
    private $chamberPatients;
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->chamberPatients = $this->container->get(ChambersPatientsService::class);
    }

    public function testMain(): void
    {
        $test = $this->chamberPatients->create(new Patients(),new Chambers());

        $this->assertObjectHasProperty('chambers',$test);
        $this->assertObjectHasProperty('patients',$test);
    }
}