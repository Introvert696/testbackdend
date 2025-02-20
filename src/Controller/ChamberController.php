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
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/chambers')]
final class ChamberController extends AbstractController
{
    public function __construct(
        private readonly ResponseHelper $responseHelper,
        private readonly ChambersRepository $chambersRepository,
        private readonly ProcedureListRepository $procedureListRepository,
        private readonly EntityManagerInterface $em,
        private readonly AdaptersService $adaptersService,
        private readonly ValidateService $validateService
    ){}
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
    public function index(): JsonResponse
    {
        $chambers = $this->chambersRepository->findAll();
        $data = $this->responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            'Chambers',
            $chambers);
        return $this->json($data);
    }
    #[Route('/{id}', name: 'show_chambers',requirements: ['id'=>Requirement::DIGITS], methods: ["GET"])]
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
    public function show(int $id): JsonResponse
    {
        $chamberResponse = new ChamberResponseDTO();
        $patients = [];
        $chamber = $this->chambersRepository->find($id);
        if(!$chamber){
            $data =  $this->responseHelper->generate(
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
        $data = $this->responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            'Chamber and he patients',
            $chamberResponse);
       return $this->json($data,$data['code']);
    }

    #[Route('/{id}/procedures', name: 'show_chambers_procedures',requirements: ['id'=>Requirement::DIGITS], methods: ["GET"])]
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
    public function showProcedures(int $id): JsonResponse
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
            $data = $this->responseHelper->generate(
                'Not Found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Procedures not found');
            return $this->json($data,$data['code']);
        }
        $data = $this->responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            'Procedures, chamber - '.$id ,
            $data);
        return $this->json($data,$data['code']);
    }
    #[Route('/{id}/procedures', name: 'update_chambers_procedures',requirements: ['id'=>Requirement::DIGITS], methods: ["POST"])]
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
    public function updateProcedures(Request $request, int $id): JsonResponse
    {
        $data = $request->getContent();

        $procedures = [];
        $chamber = $this->chambersRepository->find($id);
        $data = $this->responseHelper->checkData($data,'App\DTO\ProcListDTO[]');
        $procedureLists = $this->procedureListRepository->findBy([
            'source_type' => 'chambers',
            'source_id' => $id
        ]);
        if (!$chamber) {
            $data = $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Chamber not found'
            );
            return $this->json($data,$data['code']);
        }
        if(!$data ){
            $data = $this->responseHelper->generate(
                'Error',
                ResponseHelper::STATUS_NOT_VALID_BODY,
                'Validate error'
            );
            return $this->json($data,$data['code']);
        }
        if($procedureLists){
            foreach ($procedureLists as $pl){
                $this->em->remove($pl);
            }
        }
        foreach($data as $d){
            $proc = $this->validateService
                ->procedureListWithProcedure($d);
            if(!$proc){
                $data = $this->responseHelper->generate(
                    'Not Valid',
                    ResponseHelper::STATUS_NOT_VALID_BODY,
                    'Procedure not valid');
                return $this->json($data,$data['code']);
            }
            $procList = $this->adaptersService
                ->procListDtoToProcList($proc,$id);
            $this->em->persist($procList);
            $procedures[] = $this->adaptersService
                ->procedureListToChamberProcedureDto($procList);
        }
        $this->em->flush();

        $data = $this->responseHelper->generate(
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
    public function store(Request $request): JsonResponse
    {
        $data = $request->getContent();
        $data = $this->validateService->chambersRequestData(
            $this->responseHelper->checkData($data,'App\Entity\Chambers')
        );
        if(!$data){
            $response =  $this->responseHelper->generate(
                'Error',
                ResponseHelper::STATUS_NOT_VALID_BODY,
                'Check request body');

            return $this->json($response,$response['code']);
        }
        $chamber = $this->chambersRepository->findBy([
            'number' =>$data->getNumber()
        ]);
        if($chamber){
            $response = $this->responseHelper->generate(
                'Conflict',
                ResponseHelper::STATUS_CONFLICT,
                'Chamber is exists',
                $this->responseHelper->first($chamber));
            return $this->json($response,$response['code']);
        }
        $this->em->persist($data);
        $this->em->flush();

        $response = $this->responseHelper->generate(
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
    public function update(Request $request, int|null $id): JsonResponse
    {
        $data = $request->getContent();
        $data = $this->responseHelper->checkData($data,'App\Entity\Chambers');
        $chamber = $this->chambersRepository->find($id);
        $valid = ( (!$data) or
            (gettype($data->getNumber())!=="integer") or
            $this->chambersRepository->findBy([
                'number'=>$data->getNumber()
            ])
        );
        if(!$chamber){
            $response = $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Chamber not found');
            return $this->json($response,$response['code']);
        }
        if($valid){
            $response = $this->responseHelper->generate(
                'Error',
                ResponseHelper::STATUS_NOT_VALID_BODY,
                'Check request body');
            return $this->json($response,$response['code']);
        }
        $chamber->setNumber($data->getNumber());
        $this->em->flush();

        $response = $this->responseHelper->generate(
            'Updated',
            ResponseHelper::STATUS_OK,
            'Chamber has been updated',
            $chamber);
        return $this->json($response,$response['code']);
    }

    #[Route('/{id}', name: 'delete_chambers', requirements: ['id'=>Requirement::DIGITS], methods: ["DELETE"])]
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
    public function delete(int $id): JsonResponse
    {
        $chamber= $this->chambersRepository->find($id);
        if(!$chamber){
            $response = $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Chamber '.$id.' - not found');
            return $this->json($response,$response['code']);
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

        $response = $this->responseHelper->generate(
            'Delete',
            ResponseHelper::STATUS_OK,
            'chamber '.$id.' has been delete');
        return $this->json($response,$response['code']);
    }
}
