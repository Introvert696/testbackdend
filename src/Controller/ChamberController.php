<?php

namespace App\Controller;

use App\DTO\ResponseDTO;
use App\Services\ChambersService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/chambers')]
final class ChamberController extends AbstractController
{
    public function __construct(
        private readonly ChambersService $chambersService
    )
    {}

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
        $response = $this->chambersService->all();
        return $this->json($response);
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
    #[OA\Tag(name:"Chamber")]
    public function show(int $id): JsonResponse
    {
        $response = $this->chambersService->get($id);
       return $this->json($response,$response['code']);
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
    public function showProcedures(int $id): JsonResponse
    {
        $response = $this->chambersService->getProcedure($id);
        return $this->json($response,$response['code']);
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
    public function updateProcedures(Request $request, int $id): JsonResponse
    {
        $response = $this->chambersService->addProcedure($id,$request->getContent());
        return $this->json($response,$response['code']);
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
        $response = $this->chambersService->create($request->getContent());
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
    public function update(Request $request,int|null $id): JsonResponse
    {
        $response = $this->chambersService->update($id,$request->getContent());
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
    public function delete(int|null $id): JsonResponse
    {
        $response = $this->chambersService->delete($id);
        return $this->json($response,$response['code']);
    }
}
