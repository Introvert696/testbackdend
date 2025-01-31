<?php

namespace App\Services;

use App\DTO\ProcListDTO;
use App\Entity\Chambers;
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
        private readonly ProceduresRepository $proceduresRepository,
        private readonly ProcedureListService $procedureListService
    )
    {}
    public function all(): array
    {
        $chambers = $this->chambersRepository->findAll();
        return $this->jsonResponseHelpers->generate('Ok',200,'Chambers',$chambers);
    }
    public function get($id): array
    {
        $patients = [];
        $chamber = $this->chambersRepository->find($id);
        if($chamber){
            $chamberPatients = $chamber->getChambersPatients()->getValues();
            if($chamberPatients){
                foreach ($chamberPatients as $cp){
                    $patients[] = $cp->getPatients();
                }
            }
            $data['chamber'] = $chamber;
            $patients ? $data['patients'] = $patients : null;
            return $this->jsonResponseHelpers->generate('Ok',200,'Chamber and he patients',Array($data));
        }
        else{
            return $this->jsonResponseHelpers->generate('Not found',404,'Chamber not found');
        }

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
        $response['message'] = '';
        $chamber = $this->chambersRepository->find($id);
        if($chamber){
            foreach ($data as $d) {
                $result = $this->procedureListRepository->findBy([
                    'procedures' => $d->procedure_id,
                    'source_type' => 'chambers',
                    'source_id' => $id
                ]);
                if (!$result and $d->procedure_id) {
                    // if not found chamber procedure in procedure_list
                    $proc = $this->proceduresRepository->find($d->procedure_id);
                    if ($proc) {
                        $procedureList = $this->procedureListService->createObject($proc,$d->queue,$id);
                        $this->em->persist($procedureList);
                        $response['message'] .= ' create new entry ';
                        $response['data'][] = $procedureList;
                    } else {
                        $response['message'] .= 'procedure not found ';
                    }
                } else {
                    foreach ($result as $pl) {
                        $pl->setQueue($d->queue);
                        $response['data'][] = $pl;
                    }
                    $response['message'] .= ' update entry ';

                }
            }
            $response =$this->jsonResponseHelpers->generate('Update',200,'Chambers procedure has been update',$response['data']);
        }
        else{
            $response =$this->jsonResponseHelpers->generate('Not Found',404,'Chamber - not found');
        }

        $this->em->flush();
        return $response;
    }
    public function create(null|string $data):array
    {
        $data = $this->serializer->deserialize($data,Chambers::class,'json');
        $chamber = $this->chambersRepository->findBy([
            'number' =>$data->getNumber()
        ]);
        if(!$chamber){
            $this->em->persist($data);
            $this->em->flush();
            $response = $this->jsonResponseHelpers->generate('Create',200,'Chamber has been create',Array($data));
        }
        else{
            $response = $this->jsonResponseHelpers->generate('Error',400,'Chamber is created',$chamber);
        }

    return $response;
    }
    public function update(int $id,null|string $data):array
    {
        $data = $this->serializer->deserialize($data,Chambers::class,'json');
        if($data->getNumber()){
            $chamber = $this->chambersRepository->find($id);
            $findChamber = $this->chambersRepository->findBy([
                'number'=>$data->getNumber()
            ]);
            if(!$findChamber){
                if($chamber){
                    $chamber->setNumber($data->getNumber());
                    $this->em->flush();
                    $response = $this->jsonResponseHelpers->generate('Updated',200,'Chamber has been updated', Array($chamber));
                }
                else {
                    $response = $this->jsonResponseHelpers->generate('Not found',404,'Chamber not found');
                }
            }
            else{
                $response = $this->jsonResponseHelpers->generate('Conflict',409,'Chamber number is busy');
            }
        }
        else {
           $response = $this->jsonResponseHelpers->generate('Not found',404,'number field - not found');
       }
       return $response;
    }
    public function delete(int $id): array
    {
        $chamber= $this->chambersRepository->find($id);
        if($chamber){
            $chamberPatient = $chamber->getChambersPatients()->getValues();
            if($chamberPatient){
                foreach ($chamberPatient as $cp){
                    $this->em->remove($cp);
                }
            }
            $this->em->remove($chamber);
            $this->em->flush();
            return $this->jsonResponseHelpers->generate('Delete',200,'chamber '.$id.' has been delete');
            }

        return $this->jsonResponseHelpers->generate('Not Found',404,'Chamber '.$id.' - not found');
    }
}