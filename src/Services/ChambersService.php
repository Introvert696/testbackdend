<?php

namespace App\Services;

use App\DTO\ProcListDTO;
use App\Entity\ProcedureList;
use App\Entity\Procedures;
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
        $data = $this->serializer->deserialize($data,ProcListDTO::class,'json');
//        dd($data->procedures);
        foreach ($data->procedures as $d){

            dd($d);
            $procedureList = $this->procedureListRepository->findByProcedureQueuePatient($d->getProclistId(),$d->getProcedureId(),$d->getQueue(),$d->getPatientId());

            if(!$procedureList){
                // if row in procedure list not exist, create it new
                // insert it code in Procedure List Service
                $procList = new ProcedureList();
                $procRepository = $this->em->getRepository(Procedures::class);
                $proc = $procRepository->find($d->getProcedureId());
                if($proc){
                    $procList->setProcedures($proc);
                }
                else{
                    // if procedure not found return message
                    // "procedure not found"
                    // i have 2 way: if one field is not correct - ignore him and create or update ProcedureList
                    // second is - return error and message "check your input fields: procedure not found"
                }


                //dd($d);
            }
            else{
                // if procedure list is exists

            }
            dump($procedureList);
        }


        dd();
        return [];
    }
}