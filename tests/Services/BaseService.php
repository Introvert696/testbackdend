<?php

namespace App\Tests\Services;

use App\Repository\PatientsRepository;
use App\Repository\ProcedureListRepository;
use App\Services\AdaptersService;
use App\Services\ChambersPatientsService;
use App\Services\ResponseHelper;
use App\Services\ValidateService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseService extends KernelTestCase
{
    protected $container;
    protected $jsonResopnseHelper;
    protected $chamberPatients;
    protected $adapterService;
    protected $validateService;
    protected $procedureListRepository;
    protected $patientRepository;
    protected $em;
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->jsonResopnseHelper = $this->container->get(ResponseHelper::class);
        $this->adapterService = $this->container->get(AdaptersService::class);
        $this->chamberPatients = $this->container->get(ChambersPatientsService::class);
        $this->validateService = $this->container->get(ValidateService::class);
        $this->procedureListRepository = $this->container->get(ProcedureListRepository::class);
        $this->patientRepository = $this->container->get(PatientsRepository::class);
        $this->em = $this->container->get('doctrine.orm.entity_manager');


    }
}