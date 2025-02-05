<?php

namespace App\Services;

use App\DTO\ChamberDTO;
use App\Entity\Chambers;
use App\Entity\ProcedureList;
use App\Repository\ChambersRepository;
use App\Repository\ProcedureListRepository;
use App\DTO\ChamberProcedureDTO;
use ChamberResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class ChambersService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly JsonResponseHelper $jsonResponseHelpers,
        private readonly ChambersRepository $chambersRepository,
        private readonly SerializerInterface $serializer,
        private readonly ProcedureListRepository $procedureListRepository,
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
        $chamberResponse = new ChamberResponse();
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
            // создать новый метод
            $responseObject= new ChamberProcedureDTO();
            $responseObject->setId($pl->getProcedures()->getId());
            $responseObject->setQueue($pl->getQueue());
            $responseObject->setTitle($pl->getProcedures()->getTitle());
            $responseObject->setDesc($pl->getProcedures()->getDescription());
            $responseObject->setStatus($pl->getStatus());
            $data[] = $responseObject;
        }
        $response = $this->jsonResponseHelpers->generate('Ok',200,'Procedures, chamber - '.$id ,$data);
        if(!$data){
            $response = $this->jsonResponseHelpers->generate('Not Found',404,'Procedures not found' );
        }

        return $response;
    }
    // Рефакторить здесь ошибка логики, и не работает нормально
    public function addProcedure($id,$data): array
    {
        $procedures = [];
        $chamber = $this->chambersRepository->find($id);
        $data = $this->checkData($data);
        dd($data);
        if(!$chamber or !$data){
            return $this->jsonResponseHelpers->generate('Not Found',404,'Chamber - not found');
        }
        $procedureLists = $this->procedureListRepository->findBy([
                'source_type' => 'chambers',
                'source_id' => $id
            ]);
        // удаляем все procedure list
        if($procedureLists){
            foreach ($procedureLists as $pl){
                $this->em->remove($pl);
            }
        }
        // валидируем данные что бы все поля были заполнены, если не заполнены, то ворачиваем false либо null
        foreach ($data as $d){


            if($d === null){
                return $this->jsonResponseHelpers->generate('Not Found',404,'Procedure - not found');
            }
            $proc =$this->procedureListService->validate($d);
            $procList = $this->procedureListService->procListDtoToProcList($proc,$id);
            $this->em->persist($procList);
            // сделать коневертер для номального ответа
            $procedures[]=$this->convertToDTO($procList);
        }
        $this->em->flush();

        return $this->jsonResponseHelpers->generate('Update/Create',200,'Chambers procedure has been update',$procedures);
    }
    public function create(string $data):array
    {
        $data = $this->checkData($data);
        if($data === null){
            return $this->jsonResponseHelpers->generate('Error',400,'check your request body');
        }
        $chamber = $this->chambersRepository->findBy([
            'number' =>$data->getNumber()
        ]);
        if($chamber){
            return $this->jsonResponseHelpers->generate('Error',400,'Chamber is exists',$this->first($chamber) );
        }
        $this->em->persist($this->convertChamberDTOtoChamber($data));
        $this->em->flush();

        return $this->jsonResponseHelpers->generate('Create',200,'Chamber has been create',$data);
    }
    public function update(int $id,null|string $data):array
    {
        $data = $this->checkData($data);
//        dd($data);
        if(gettype($data->getNumber())==="integer"){
            $chamber = $this->chambersRepository->find($id);
            $findChamber = $this->chambersRepository->findBy([
                'number'=>$data->getNumber()
            ]);
//            dd($findChamber);
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
            return $this->jsonResponseHelpers->generate('Not Found',404,'Chamber '.$id.' - not found');
        }
        $chamberPatient = $chamber->getChambersPatients()->getValues();
        if($chamberPatient){
            // если есть то удаляем записи
            foreach ($chamberPatient as $cp){
                $this->em->remove($cp);
            }
        }
        // теперь нужно удалить найти записи в procedure_list
        $procedureLists = $this->procedureListRepository->findBy([
            'source_id' => $chamber->getId(),
            'source_type' => 'chambers'
        ]);
        if($procedureLists)
        {
            // удаляем все процедуры которые связаны с палатами
            foreach ($procedureLists as $pl){
                $this->em->remove($pl);
            }
        }
        // удаляем палату
        $this->em->remove($chamber);
        $this->em->flush();
        return $this->jsonResponseHelpers->generate('Delete',200,'chamber '.$id.' has been delete');
    }
    //ProcedureList to ProcedureListDTO
    public function convertToDTO(ProcedureList $procList): ChamberProcedureDTO
    {
        $procListDTO = new ChamberProcedureDTO();
        $procListDTO->setId($procList->getProcedures()->getId());
        $procListDTO->setStatus($procList->getStatus());
        $procListDTO->setQueue($procList->getQueue());
        $procListDTO->setTitle($procList->getProcedures()->getTitle());
        $procListDTO->setDesc($procList->getProcedures()->getDescription());

        return $procListDTO;
    }
    //convert chamberDTO to Chamber
    public function convertChamberDTOtoChamber(ChamberDTO $chamberDTO):Chambers
    {
        $chamber = new Chambers();
        $chamber->setNumber($chamberDTO->number);

        return $chamber;
    }
    public function checkData($data): array|null
    {

        try{
            $data = $this->serializer->deserialize($data,ChamberDTO::class.'[]','json');
        }
        catch (NotEncodableValueException){
            return null;
        }

        return $data;
    }
    public function first(array $data): object
    {
        return $data[0];
    }

}