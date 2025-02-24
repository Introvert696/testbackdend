<?php

namespace App\Controller;

use App\DTO\Chamber\ChamberResponseDTO;
use App\DTO\ResponseDTO;
use App\Repository\ChambersRepository;
use App\Repository\ProcedureListRepository;
use App\Services\AdaptersService;
use App\Services\ResponseFabric;
use App\Services\ResponseHelper;
use App\Services\ValidateService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
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
        private readonly ValidateService $validateService,
        private readonly ResponseFabric $responseFabric,
    ){}
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
    #[Route('/', name: 'index_chambers', methods: ["GET"])]
    public function index(): JsonResponse
    {
        $response = $this->responseFabric->ok('All chambers',$this->chambersRepository->findAll());
        return $this->json($response);
    }

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
    #[Route('/{id}', name: 'show_chambers',requirements: ['id'=>Requirement::DIGITS], methods: ["GET"])]
    public function show(int $id): JsonResponse
    {
        $chamberResponse = new ChamberResponseDTO();
        $patients = [];
        $chamber = $this->chambersRepository->find($id);
        if(!$chamber){
            $response = $this->responseFabric->notFound('Chamber - not found');

            return $this->json($response,$response['code']);
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
        $response = $this->responseFabric->ok('Chamber info',$chamberResponse);

       return $this->json($response,$response['code']);
    }
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
    #[Route('/{id}/procedures', name: 'show_chambers_procedures',requirements: ['id'=>Requirement::DIGITS], methods: ["GET"])]
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
            $response = $this->responseFabric->notFound('Procedures not found');
            return $this->json($response,$response['code']);
        }
        $response = $this->responseFabric->ok('Procedures, chamber - '.$id ,$data);

        return $this->json($response,$response['code']);
    }
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
    #[Route('/{id}/procedures', name: 'update_chambers_procedures',requirements: ['id'=>Requirement::DIGITS], methods: ["POST"])]
    public function updateProcedures(Request $request, int $id): JsonResponse
    {
        $procedures = [];
        $chamber = $this->chambersRepository->find($id);
        $procListDTO = $this->responseHelper->checkData($request->getContent(), 'App\DTO\Chamber\ProcListDTO[]');
        $procedureLists = $this->procedureListRepository->findBy([
            'source_type' => 'chambers',
            'source_id' => $id
        ]);
        if (!$chamber) {
            $response = $this->responseFabric->notFound('Chamber - not found');

            return $this->json($response, $response['code']);
        }
        if (!$procListDTO) {
            $response = $this->responseFabric->notValid();

            return $this->json($response, $response['code']);
        }
        if ($procedureLists) {
            foreach ($procedureLists as $pl) {
                $this->em->remove($pl);
            }
        }
        foreach ($procListDTO as $d) {
            $proc = $this->validateService
                ->procedureListWithProcedure($d);
            if (!$proc) {
                $response = $this->responseFabric->notValid();

                return $this->json($response, $response['code']);
            }
            $procList = $this->adaptersService
                ->procListDtoToProcList($proc, $id);
            $this->em->persist($procList);
            $procedures[] = $this->adaptersService
                ->procedureListToChamberProcedureDto($procList);
        }
        $this->em->flush();
        $response = $this->responseFabric->ok('Chambers procedure has been update', $procedures);

        return $this->json($response, $response['code']);
    }
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
    #[Route(name: 'store_chambers', methods: ["POST"])]
    public function store(Request $request): JsonResponse
    {
        $data = $this->validateService->chambersRequestData(
            $this->responseHelper->checkData($request->getContent(),'App\Entity\Chambers')
        );
        if(!$data){
            $response = $this->responseFabric->notValid();
            return $this->json($response,$response['code']);
        }
        $chamber = $this->chambersRepository->findBy([
            'number' =>$data->getNumber()
        ]);
        if($chamber){
            $response = $this->responseFabric->conflict($this->responseHelper->first($chamber));
            return $this->json($response,$response['code']);
        }
        $this->em->persist($data);
        $this->em->flush();
        $response = $this->responseFabric->ok('Chamber has been create',$data);

        return $this->json($response,$response['code']);
    }
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
    #[Route('/{id}', name: 'update_chambers', methods: ["PATCH"])]
    public function update(Request $request, int|null $id): JsonResponse
    {
        $data = $this->responseHelper->checkData($request->getContent(),'App\Entity\Chambers');
        $chamber = $this->chambersRepository->find($id);
        $valid = ( (!$data) or
            (gettype($data->getNumber())!=="integer") or
            $this->chambersRepository->findBy([
                'number'=>$data->getNumber()
            ])
        );
        if(!$chamber){
            $response = $this->responseFabric->notFound('Chamber - not found');
            return $this->json($response,$response['code']);
        }
        if($valid){
            $response = $this->responseFabric->notValid();
            return $this->json($response,$response['code']);
        }
        $chamber->setNumber($data->getNumber());
        $this->em->flush();
        $response = $this->responseFabric->ok('Chamber has been update');

        return $this->json($response,$response['code']);
    }
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
    #[Route('/{id}', name: 'delete_chambers', requirements: ['id'=>Requirement::DIGITS], methods: ["DELETE"])]
    public function delete(int $id): JsonResponse
    {
        $chamber= $this->chambersRepository->find($id);
        if(!$chamber){
            $response = $this->responseFabric->notFound('Chamber - not found');
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
        $response = $this->responseFabric->ok('Chamber - has been delete');
        return $this->json($response,$response['code']);
    }
}
