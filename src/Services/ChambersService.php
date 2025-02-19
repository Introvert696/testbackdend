<?php

namespace App\Services;

use App\DTO\ChamberResponseDTO;
use App\Repository\ChambersRepository;
use App\Repository\ProcedureListRepository;
use Doctrine\ORM\EntityManagerInterface;

class ChambersService
{
    public function __construct(
        private readonly EntityManagerInterface  $em,
        private readonly ResponseHelper          $responseHelper,
        private readonly ChambersRepository      $chambersRepository,
        private readonly ProcedureListRepository $procedureListRepository,
        private readonly AdaptersService         $adaptersService,
        private readonly ValidateService         $validator,
    ){}
    public function addProcedure($id,$data): array
    {
        $procedures = [];
        $chamber = $this->chambersRepository->find($id);
        $data = $this->responseHelper->checkData($data,'App\DTO\ProcListDTO[]');
        $procedureLists = $this->procedureListRepository->findBy([
            'source_type' => 'chambers',
            'source_id' => $id
        ]);
        if (!$chamber) {
            return $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Chamber not found'
            );
        }
        if(!$data ){
            return $this->responseHelper->generate(
                'Error',
                ResponseHelper::STATUS_NOT_VALID_BODY,
                'Validate error'
            );
        }
        if($procedureLists){
            foreach ($procedureLists as $pl){
                $this->em->remove($pl);
            }
        }
        foreach($data as $d){
            $proc = $this->validator
                ->procedureListWithProcedure($d);
            if(!$proc){
                return $this->responseHelper->generate(
                    'Not Valid',
                    ResponseHelper::STATUS_NOT_VALID_BODY,
                    'Procedure not valid');
            }
            $procList = $this->adaptersService
                ->procListDtoToProcList($proc,$id);
            $this->em->persist($procList);
            $procedures[] = $this->adaptersService
                ->procedureListToChamberProcedureDto($procList);
        }
        $this->em->flush();

        return $this->responseHelper->generate(
            'Update',
            ResponseHelper::STATUS_OK,
            'Chambers procedure has been update',
            $procedures);
    }
    public function all(): array
    {
        $chambers = $this->chambersRepository->findAll();
        return $this->responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            'Chambers',
            $chambers);
    }
    public function get($id): array
    {
        $chamberResponse = new ChamberResponseDTO();
        $patients = [];
        $chamber = $this->chambersRepository->find($id);
        if(!$chamber){
            return $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Chamber not found');
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
        return $this->responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            'Chamber and he patients',
            $chamberResponse);
    }
    public function getProcedure(int $id): array
    {
        $data = [];
        $procList = $this->procedureListRepository->findBy([
            'source_type'=>'chambers',
            'source_id'=>$id
        ]);
        foreach ($procList as $pl){
            $data[] = $this->adaptersService
                ->procedureListToChamberProcedureDTO($pl);
        }
        if(!$data){
            return $this->responseHelper->generate(
                'Not Found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Procedures not found');
        }
        return $this->responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            'Procedures, chamber - '.$id ,
            $data);
    }

    public function create(string $data):array
    {
        $data = $this->validator->chambersRequestData(
            $this->responseHelper->checkData($data,'App\Entity\Chambers')
        );
        if(!$data){
            return $this->responseHelper->generate(
                'Error',
                ResponseHelper::STATUS_NOT_VALID_BODY,
                'Check request body');
        }
        $chamber = $this->chambersRepository->findBy([
            'number' =>$data->getNumber()
        ]);
        if($chamber){
            return $this->responseHelper->generate(
                'Conflict',
                ResponseHelper::STATUS_CONFLICT,
                'Chamber is exists',
                $this->responseHelper->first($chamber));
        }
        $this->em->persist($data);
        $this->em->flush();

        return $this->responseHelper->generate(
            'Create',
            ResponseHelper::STATUS_OK,
            'Chamber has been create',
            $data);
    }
    public function update(int $id,null|string $data):array
    {
        $data = $this->responseHelper->checkData($data,'App\Entity\Chambers');
        $chamber = $this->chambersRepository->find($id);
        // переделать
        $valid = ( (!$data) or
                    (gettype($data?->getNumber())!=="integer") or
                    $this->chambersRepository->findBy([
                        'number'=>$data->getNumber()
                    ])
        );
        if(!$chamber){
            return $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Chamber not found');
        }
        if($valid){
            return $this->responseHelper->generate(
                'Error',
                ResponseHelper::STATUS_NOT_VALID_BODY,
                'Check request body');
        }
        $chamber->setNumber($data->getNumber());
        $this->em->flush();

        return $this->responseHelper->generate(
            'Updated',
            ResponseHelper::STATUS_OK,
            'Chamber has been updated',
            $chamber);
    }
    public function delete(int $id): array
    {
        $chamber= $this->chambersRepository->find($id);
        if(!$chamber){
            return $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Chamber '.$id.' - not found');
        }
        $chamberPatient = $chamber->getChambersPatients()->getValues();
        if($chamberPatient){
            foreach ($chamberPatient as $cp)
            {
                $this->em->remove($cp);
            }
        }
        $procedureLists = $this->procedureListRepository->findBy([
            'source_id' => $chamber->getId(),
            'source_type' => 'chambers'
        ]);
        if($procedureLists){
            foreach ($procedureLists as $pl){
                $this->em->remove($pl);
            }
        }
        $this->em->remove($chamber);
        $this->em->flush();

        return $this->responseHelper->generate(
            'Delete',
            ResponseHelper::STATUS_OK,
            'chamber '.$id.' has been delete');
    }

}