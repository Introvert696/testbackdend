<?php

namespace App\Controller;

use App\DTO\ChamberResponseDTO;
use App\DTO\ResponseDTO;
use App\Repository\ChambersRepository;
use App\Repository\ProcedureListRepository;
use App\Services\AdaptersService;
use App\Services\ResponseHelper;
use App\Services\ValidateService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/chambers')]
final class ChamberController extends AbstractController
{
    #[Route('/', name: 'index_chambers', methods: ["GET"])]
    #[OA\Response(
            response: 200,
            description: 'Return all chambers',
            content: new OA\JsonContent(
                ref: new Model(type:ResponseDTO::class),
                type: 'object',
                example: [
                    "type"=>"ok",
                    "code"=>200,
                    "message" => "chamber and patients",
                    "data" =>[
                       [
                           "id"=>1,
                           "number" =>233,
                       ],
                        [
                            "id"=>1,
                            "number" =>233,
                        ]
                    ]
                ]
        )
    )]
    #[OA\Tag(name:"Chamber")]
    public function index(ChambersRepository $chambersRepository,ResponseHelper $responseHelper): JsonResponse
    {
        $chambers = $chambersRepository->findAll();
        $data = $responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            'Chambers',
            $chambers);
        return $this->json($data);
    }
    #[Route('/{id}', name: 'show_chambers', methods: ["GET"])]
    #[OA\Response(
        response: 200,
        description: 'Return all chambers',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"ok",
                "code"=>200,
                "message" => "chamber and he patients",
                "data" =>[
                    "id"=>1,
                    "number" =>233,
                    "patients" =>[]
                ]
            ]
        ),
    )]
    #[OA\Response(
        response: 404,
        description: 'Return all chambers',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not found",
                "code"=>404,
                "message" => "Chamber not found",
            ]
        ),
    )]
    #[OA\Tag(name:"Chamber")]
    public function show(int $id,ChambersRepository $chambersRepository, ResponseHelper $responseHelper): JsonResponse
    {
        $chamberResponse = new ChamberResponseDTO();
        $patients = [];
        $chamber = $chambersRepository->find($id);
        if(!$chamber){
            $data =  $responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Chamber not found');
            return $this->json($data,$data['code']);
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
        $data = $responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            'Chamber and he patients',
            $chamberResponse);
       return $this->json($data,$data['code']);
    }

    #[Route('/{id}/procedures', name: 'show_chambers_procedures', methods: ["GET"])]
    #[OA\Response(
        response: 200,
        description: 'Return all chambers',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"ok",
                "code"=>200,
                "message" => "chamber and he patients",
                "data" =>[
                    "id"=>1,
                    "number" =>233,
                    "data" =>[
                        [
                            "id"=>1,
                            "title" =>'Эхокардиография',
                            "desc" =>'Взять ',
                            "queue" =>1,
                            "status" =>false
                        ],
                        [
                            "id"=>1,
                            "title" =>'Эхокардиография',
                            "desc" =>'Взять ',
                            "queue" =>1,
                            "status" =>false
                        ]
                    ]
                ]
            ]
        ),
    )]
    #[OA\Tag(name:"Chamber")]
    #[OA\Response(
        response: 404,
        description: 'Not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not Found",
                "code"=>404,
                "message" => "Chamber procedures",

            ]
        ),
    )]
    public function showProcedures(
        int $id,
        ProcedureListRepository $procedureListRepository,
        AdaptersService $adaptersService,
        ResponseHelper $responseHelper
    ): JsonResponse
    {
        $data = [];
        $procList = $procedureListRepository->findBy([
            'source_type'=>'chambers',
            'source_id'=>$id
        ]);
        foreach ($procList as $pl){
            $data[] = $adaptersService
                ->procedureListToChamberProcedureDTO($pl);
        }
        if(!$data){
            $data = $responseHelper->generate(
                'Not Found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Procedures not found');
            return $this->json($data,$data['code']);
        }
        $data = $responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            'Procedures, chamber - '.$id ,
            $data);
        return $this->json($data,$data['code']);
    }
    #[Route('/{id}/procedures', name: 'update_chambers_procedures', methods: ["POST"])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                type: 'object',
            ),
            example: [
                [
                    'procedure_id' => 1,
                    'queue'=>3,
                    'status'=>true
                ],
                [
                    'procedure_id' => 2,
                    'queue'=>3,
                    'status'=>true
                ],
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not Found",
                "code"=>404,
                "message" => "Chambers not found",

            ]
        ),
    )]
    #[OA\Response(
        response: 402,
        description: 'Fields Error',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Error",
                "code"=>404,
                "message" => "Check fields",

            ]
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Updated',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Ok",
                "code"=>200,
                "message" => "Procedure list has been update",

            ]
        ),
    )]
    #[OA\Tag(name:"Chamber")]
    public function updateProcedures(
        Request $request,
        int $id,
        ChambersRepository $chambersRepository,
        ResponseHelper $responseHelper,
        ProcedureListRepository $procedureListRepository,
        EntityManagerInterface $em,
        ValidateService $validateService,
        AdaptersService $adaptersService): JsonResponse
    {
        $data = $request->getContent();

        $procedures = [];
        $chamber = $chambersRepository->find($id);
        $data = $responseHelper->checkData($data,'App\DTO\ProcListDTO[]');
        $procedureLists = $procedureListRepository->findBy([
            'source_type' => 'chambers',
            'source_id' => $id
        ]);
        if (!$chamber) {
            $data = $responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Chamber not found'
            );
            return $this->json($data,$data['code']);
        }
        if(!$data ){
            $data = $responseHelper->generate(
                'Error',
                ResponseHelper::STATUS_NOT_VALID_BODY,
                'Validate error'
            );
            return $this->json($data,$data['code']);
        }
        if($procedureLists){
            foreach ($procedureLists as $pl){
                $em->remove($pl);
            }
        }
        foreach($data as $d){
            $proc = $validateService
                ->procedureListWithProcedure($d);
            if(!$proc){
                $data = $responseHelper->generate(
                    'Not Valid',
                    ResponseHelper::STATUS_NOT_VALID_BODY,
                    'Procedure not valid');
                return $this->json($data,$data['code']);
            }
            $procList = $adaptersService
                ->procListDtoToProcList($proc,$id);
            $em->persist($procList);
            $procedures[] = $adaptersService
                ->procedureListToChamberProcedureDto($procList);
        }
        $em->flush();

        $data = $responseHelper->generate(
            'Update',
            ResponseHelper::STATUS_OK,
            'Chambers procedure has been update',
            $procedures);

        return $this->json($data,$data['code']);
    }
    #[Route(name: 'store_chambers', methods: ["POST"])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                type: 'object',
            ),
            example:
                [
                    'number' => 1,
                ],
        ))]
    #[OA\Response(
        response: 402,
        description: 'Error',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Error",
                "code"=>402,
                "message" => "Check request body",

            ]
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Error',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Error",
                "code"=>400,
                "message" => "Chamber is exists",
                "data"=>[
                    'id' => 23,
                    "number"=>1
                ]

            ]
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Create',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Create",
                "code"=>200,
                "message" => "Chamber has been create",
                "data"=>[
                    'id' => 24,
                    "number"=>11
                ]

            ]
        ),
    )]
    #[OA\Tag(name:"Chamber")]
    public function store(
        Request $request,
        ValidateService $validateService,
        ResponseHelper $responseHelper,
        ChambersRepository $chambersRepository,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $data = $request->getContent();
        $data = $validateService->chambersRequestData(
            $responseHelper->checkData($data,'App\Entity\Chambers')
        );
        if(!$data){
            $response =  $responseHelper->generate(
                'Error',
                ResponseHelper::STATUS_NOT_VALID_BODY,
                'Check request body');

            return $this->json($response,$response['code']);
        }
        $chamber = $chambersRepository->findBy([
            'number' =>$data->getNumber()
        ]);
        if($chamber){
            $response = $responseHelper->generate(
                'Conflict',
                ResponseHelper::STATUS_CONFLICT,
                'Chamber is exists',
                $responseHelper->first($chamber));
            return $this->json($response,$response['code']);
        }
        $em->persist($data);
        $em->flush();

        $response = $responseHelper->generate(
            'Create',
            ResponseHelper::STATUS_OK,
            'Chamber has been create',
            $data);
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}', name: 'update_chambers', methods: ["PATCH"])]
    #[OA\RequestBody(
      required: true,
        content: new OA\JsonContent(
            properties: [
                "number"=>new OA\Property(type:'integer',example: '2')
        ],
            type: "string", example: [
                "number"=>333
              ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not Found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not found",
                "code"=>404,
                "message" => "Chamber not found",
            ]
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Updated',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Updated",
                "code"=>200,
                "message" => "Chamber has been updated",
                "data"=>[
                    "id"=>1,
                    "number" => 333
                ]
            ]
        ),
    )]
    #[OA\Tag(name:"Chamber")]
    public function update(
        Request $request,
        int|null $id,
        ResponseHelper $responseHelper,
        ChambersRepository $chambersRepository,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $data = $request->getContent();
        $data = $responseHelper->checkData($data,'App\Entity\Chambers');
        $chamber = $chambersRepository->find($id);
        $valid = ( (!$data) or
            (gettype($data->getNumber())!=="integer") or
            $chambersRepository->findBy([
                'number'=>$data->getNumber()
            ])
        );
        if(!$chamber){
            $response = $responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Chamber not found');
            return $this->json($response,$response['code']);
        }
        if($valid){
            $response = $responseHelper->generate(
                'Error',
                ResponseHelper::STATUS_NOT_VALID_BODY,
                'Check request body');
            return $this->json($response,$response['code']);
        }
        $chamber->setNumber($data->getNumber());
        $em->flush();

        $response = $responseHelper->generate(
            'Updated',
            ResponseHelper::STATUS_OK,
            'Chamber has been updated',
            $chamber);
        return $this->json($response,$response['code']);
    }

    #[Route('/{id}', name: 'delete_chambers', methods: ["DELETE"])]
    #[OA\Response(
        response: 202,
        description: 'Return all chambers',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Delete",
                "code"=>202,
                "message" => "Chamber has been delete",
            ]
        ),
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not Found",
                "code"=>404,
                "message" => "Chamber - not found",
            ]
        ),
    )]
    #[OA\Tag(name:"Chamber")]
    public function delete(
        int|null $id,
        ChambersRepository $chambersRepository,
        ResponseHelper $responseHelper,
        EntityManagerInterface $em,
        ProcedureListRepository $procedureListRepository,
    ): JsonResponse
    {

        $chamber= $chambersRepository->find($id);
        if(!$chamber){
            $response = $responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Chamber '.$id.' - not found');
            return $this->json($response,$response['code']);
        }
        $chamberPatient = $chamber->getChambersPatients()->getValues();
        if($chamberPatient){
            foreach ($chamberPatient as $cp)
            {
                $em->remove($cp);
            }
        }
        $procedureLists = $procedureListRepository->findBy([
            'source_id' => $chamber->getId(),
            'source_type' => 'chambers'
        ]);
        if($procedureLists){
            foreach ($procedureLists as $pl){
                $em->remove($pl);
            }
        }
        $em->remove($chamber);
        $em->flush();

        $response = $responseHelper->generate(
            'Delete',
            ResponseHelper::STATUS_OK,
            'chamber '.$id.' has been delete');
        return $this->json($response,$response['code']);
    }
}
