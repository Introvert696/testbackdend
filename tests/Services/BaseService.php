<?php

namespace App\Tests\Services;

use App\Repository\PatientsRepository;
use App\Repository\ProcedureListRepository;
use App\Services\AdaptersService;
use App\Services\ChambersPatientsService;
use App\Services\ChambersService;
use App\Services\ResponseHelper;
use App\Services\PatientsServices;
use App\Services\ProceduresService;
use App\Services\ValidateService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseService extends KernelTestCase
{
    protected $container;
    protected $jsonResopnseHelper;
    protected $chamberService;
    protected $chamberPatients;
    protected $adapterService;
    protected $patientsServices;
    protected $procedureService;
    protected $validateService;
    protected $procedureListRepository;
    protected $patientRepository;
    protected $em;
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->jsonResopnseHelper = $this->container->get(ResponseHelper::class);
        $this->chamberService = $this->container->get(ChambersService::class);
        $this->adapterService = $this->container->get(AdaptersService::class);
        $this->chamberPatients = $this->container->get(ChambersPatientsService::class);
        $this->patientsServices = $this->container->get(PatientsServices::class);
        $this->procedureService = $this->container->get(ProceduresService::class);
        $this->validateService = $this->container->get(ValidateService::class);
        $this->procedureListRepository = $this->container->get(ProcedureListRepository::class);
        $this->patientRepository = $this->container->get(PatientsRepository::class);
        $this->em = $this->container->get('doctrine.orm.entity_manager');


    }
}