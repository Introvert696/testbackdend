<?php

namespace App\Tests\Services;

use App\Services\AdaptersService;
use App\Services\ChambersPatientsService;
use App\Services\ChambersService;
use App\Services\JsonResponseHelper;
use App\Services\PatientsServices;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseService extends KernelTestCase
{
    protected $container;
    protected $jsonResopnseHelper;
    protected $chamberService;
    protected $chamberPatients;
    protected $adapterService;
    protected $patientsServices;
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->jsonResopnseHelper = $this->container->get(JsonResponseHelper::class);
        $this->chamberService = $this->container->get(ChambersService::class);
        $this->adapterService = $this->container->get(AdaptersService::class);
        $this->chamberPatients = $this->container->get(ChambersPatientsService::class);
        $this->patientsServices = $this->container->get(PatientsServices::class);
    }
}