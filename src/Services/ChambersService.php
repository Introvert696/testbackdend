<?php

namespace App\Services;

use App\DTO\ChamberResponseDTO;
use App\Repository\ChambersRepository;
use App\Repository\ProcedureListRepository;
use Doctrine\ORM\EntityManagerInterface;

class ChambersService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly JsonResponseHelper $jsonResponseHelpers,
        private readonly ChambersRepository $chambersRepository,
        private readonly ProcedureListRepository $procedureListRepository,
        private readonly AdaptersService $adaptersService,
        private readonly ValidateService $validator,
    )
    {}
    public function addProcedure($id,$data): array
    {
        $procedures = [];
        $chamber = $this->chambersRepository->find($id);
        $data = $this->jsonResponseHelpers->checkData($data,'App\DTO\ProcListDTO[]');

        if(!$data)
        {
            return $this->jsonResponseHelpers->generate('Error',422,'Check fields (data)');
        }
        foreach ($data as $d){
            if(!($this->validator->procListDTO($d))){
                return $this->jsonResponseHelpers->generate('Error',402,'Check fields (validator)');
            }

        }
        if(!$chamber){
            return $this->jsonResponseHelpers->generate('Error',404,'Chamber not found');
        }
        $procedureLists = $this->procedureListRepository->findBy([
            'source_type' => 'chambers',
            'source_id' => $id
        ]);
        if($procedureLists){
            foreach ($procedureLists as $pl){
                $this->em->remove($pl);
            }
        }
        foreach ($data as $d){
            if($d === null){
                return $this->jsonResponseHelpers->generate('Not Found',404,'Procedure - not found');
            }
            $proc =$this->validator->procedureListWithProcedure($d);
            if(!$proc){
                return $this->jsonResponseHelpers->generate('Not Valid',502,'Procedure not valid');
            }
            $procList = $this->adaptersService->procListDtoToProcList($proc,$id);
            $this->em->persist($procList);
            $procedures[]=$this->adaptersService->procedureListToChamberProcedureDto($procList);
        }
        $this->em->flush();

        return $this->jsonResponseHelpers->generate('Update',200,'Chambers procedure has been update',$procedures);
    }
    public function all(): array
    {
        $chambers = $this->chambersRepository->findAll();
        return $this->jsonResponseHelpers->generate('Ok',200,'Chambers',$chambers);
    }
    public function get($id): array
    {
        $chamberResponse = new ChamberResponseDTO();
        $patients = [];
        $chamber = $this->chambersRepository->find($id);
        if(!$chamber) {
            return $this->jsonResponseHelpers->generate('Not found',404,'Chamber not found');
        }

        $chamberPatients = $chamber->getChambersPatients()->getValues();
        if($chamberPatients){
            foreach ($chamberPatients as $cp){
                $patients[] = $cp->getPatients();
            }
            $chamberResponse->setPatients($patients);
        }
        $chamberResponse->setId($chamber->getId());
        $chamberResponse->setNumber($chamber->getNumber());
        return $this->jsonResponseHelpers->generate('Ok',200,'Chamber and he patients',$chamberResponse);
    }
    public function getProcedure(int $id): array
    {
        $procList = $this->procedureListRepository->findBy(['source_type'=>'chambers','source_id'=>$id]);
        $data = [];
        foreach ($procList as $pl){
            $data[] = $this->adaptersService->procedureListToChamberProcedureDTO($pl);
        }
        $response = $this->jsonResponseHelpers->generate('Ok',200,'Procedures, chamber - '.$id ,$data);
        if(!$data){
            $response = $this->jsonResponseHelpers->generate('Not Found',404,'Procedures not found' );
        }

        return $response;
    }

    public function create(string $data):array
    {
        $data = $this->jsonResponseHelpers->checkData($data,'App\Entity\Chambers');
        if($data === null){
            return $this->jsonResponseHelpers->generate('Error',409,'check your request body');
        }
        $data = $this->validator->chambersRequestData($data);
        if(!$data){
            return $this->jsonResponseHelpers->generate('Error',409,'check your request body');
        }
        $chamber = $this->chambersRepository->findBy([
            'number' =>$data->getNumber()
        ]);
        if($chamber){
            return $this->jsonResponseHelpers->generate('Error',400,'Chamber is exists',$this->jsonResponseHelpers->first($chamber) );
        }
        $this->em->persist($data);
        $this->em->flush();

        return $this->jsonResponseHelpers->generate('Create',200,'Chamber has been create',$data);
    }
    public function update(int $id,null|string $data):array
    {
        $data = $this->jsonResponseHelpers->checkData($data,'App\Entity\Chambers');

        if(!$data){
            return $this->jsonResponseHelpers->generate('Error',400,'check your request body');
        }
        if(gettype($data->getNumber())==="integer"){
            $chamber = $this->chambersRepository->find($id);
            $findChamber = $this->chambersRepository->findBy([
                'number'=>$data->getNumber()
            ]);
            if($findChamber) {
                return $this->jsonResponseHelpers->generate('Conflict',409,'Chamber number is busy');
            }
            if(!$chamber){
                return $this->jsonResponseHelpers->generate('Not found',404,'Chamber not found');
            }
            $chamber->setNumber($data->getNumber());
            $this->em->flush();
            return $this->jsonResponseHelpers->generate('Updated',200,'Chamber has been updated', $chamber);
        }
       return  $this->jsonResponseHelpers->generate('Not found',404,'number field - not found');
    }
    public function delete(int $id): array
    {
        $chamber= $this->chambersRepository->find($id);
        if(!$chamber){
            return $this->jsonResponseHelpers->generate('Not found',404,'Chamber '.$id.' - not found');
        }
        $chamberPatient = $chamber->getChambersPatients()->getValues();
        if($chamberPatient){
            foreach ($chamberPatient as $cp){
                $this->em->remove($cp);
            }
        }
        $procedureLists = $this->procedureListRepository->findBy([
            'source_id' => $chamber->getId(),
            'source_type' => 'chambers'
        ]);
        if($procedureLists)
        {
            foreach ($procedureLists as $pl){
                $this->em->remove($pl);
            }
        }
        $this->em->remove($chamber);
        $this->em->flush();
        return $this->jsonResponseHelpers->generate('Delete',202,'chamber '.$id.' has been delete');
    }

}