<?php

namespace App\Services;

use App\DTO\ChamberDTO;
use App\Repository\ChambersRepository;
use App\Repository\ProcedureListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ChambersService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly JsonResponseHelper $jsonResponseHelpers,
        private readonly ChambersRepository $chambersRepository,
        private readonly SerializerInterface $serializer,
        private readonly ProcedureListRepository $procedureListRepository,
    )
    {}
    public function get($id): array
    {
        $patients = [];
        $chamber = $this->chambersRepository->find($id);
        $chamberPatients = $chamber->getChambersPatients()->getValues();

        foreach ($chamberPatients as $cp){
            $patients[] = $cp->getPatients();
        }
        $data['chamber'] = $chamber;
        $data['patients'] = $patients;

        return $this->jsonResponseHelpers->generate('Ok',200,'Chamber and he patients',$data);
    }
    public function getProcedure($id): array
    {
        $query = $this->em->createQuery(
            'SELECT pr.id,pr.title from App\Entity\ProcedureList pl
                  JOIN pl.procedures pr
                  JOIN pl.patients p
                  JOIN p.chambersPatients cp
                  join cp.chambers c
                  where c.id = 1'
        );
        $result =$query->getResult();

        return $this->jsonResponseHelpers->generate('Ok',200,'Procedures, chamber - '.$id ,$result);
    }
    public function addProcedure($id,$data): array
    {
        $data = $this->serializer->deserialize($data,ChamberDTO::class.'[]','json');
        // check it in table, if exist update data
        //
        foreach ($data as $d){
            dump($this->procedureListRepository->findByProcedureQueuePatient($d->getProcedureId(),$d->getQueue(),$d->getPatientId()));
        }

        dd();
        return [];
    }
}