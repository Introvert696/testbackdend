<?php

namespace App\Services;

use App\DTO\ProcListDTO;
use App\Entity\ProcedureList;
use App\Repository\ChambersRepository;
use App\Repository\ProcedureListRepository;
use App\Repository\ProceduresRepository;
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
        private readonly ProceduresRepository $proceduresRepository

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
    public function getProcedure(int $id): array
    {
        $procList = $this->procedureListRepository->findBy(['source_type'=>'chambers','source_id'=>$id]);
        $procedures = [];
        foreach ($procList as $pl){
            $procedures[]=$pl;
        }
        $response = $this->jsonResponseHelpers->generate('Ok',200,'Procedures, chamber - '.$id ,$procedures);
        if(!$procedures){
            $response = $this->jsonResponseHelpers->generate('Not Found',404,'Procedures not found' );
        }

        return $response;
    }
    public function addProcedure($id,$data): array
    {
        $data = $this->serializer->deserialize($data,ProcListDTO::class.'[]','json');
//        dd($data);
        foreach ($data as $d) {
            // check if data, don't have all input fields
            // return error with message - 'send all input fields'
            $result = $this->procedureListRepository->findBy([
                'procedures'=>$d->procedure_id,
                'source_id'=>$d->source_id,
                'source_type' =>$d->source_type,
                ]);
            dump($result,"result");
            if(!$result){
                $procList = new ProcedureList();
                $procList->setProcedures($this->proceduresRepository->find($d->procedure_id));
                $procList->setQueue($d->queue);
                $procList->setSourceType($d->source_type);
                $procList->setSourceId($d->source_id);
                $this->em->persist($procList);
            }
            foreach ($result as $pl){
                $pl->setQueue($d->queue);
                $this->em->persist($pl);
            }
            dump($result);
            $this->em->flush();

        }
        dump($id);
//        dd($procList);
        return [];
    }
}