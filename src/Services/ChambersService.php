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
        $responseData = [];
        $responseData['message'] = '';

        foreach ($data as $d) {
            $result = $this->procedureListRepository->findBy([
                'procedures' => $d->procedure_id,
                'source_type' => 'chambers',
                'source_id' => $id
            ]);
            if (!$result) {
                // if not found chamber procedure in procedure_list
                $proc = $this->proceduresRepository->find($d->procedure_id);
                if ($proc) {
                    $procedureList = new ProcedureList();
                    $procedureList->setProcedures($proc);
                    $procedureList->setQueue($d->queue);
                    $procedureList->setSourceType("chambers");
                    $procedureList->setSourceId($id);
                    $procedureList->setStatus(false);

                    $this->em->persist($procedureList);

                    $responseData['message'] .= ' create new entry ';
                    $responseData['data'][] = $procedureList;
                } else {
                    $responseData['message'] .= 'procedure not found ';
                }

            } else {
                foreach ($result as $pl) {
                    $pl->setQueue($d->queue);
                }
                $responseData['message'] .= ' update entry ';
                $responseData['data'][] = $result;
            }
        }
        $this->em->flush();
        return $this->jsonResponseHelpers->generate('Create/update procedure',200,$responseData['message'],$responseData['data']);
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
            $chamber->setNumber($data->getNumber());
            $this->em->flush();
            $response = $this->jsonResponseHelpers->generate('Updated',200,'Chamber has been updated', Array($chamber));
        }
        else {
           $response = $this->jsonResponseHelpers->generate('No found',404,'number field - not found');
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