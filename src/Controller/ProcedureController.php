<?php

namespace App\Controller;

use App\DTO\ResponseDTO;
use App\Services\ProceduresService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/procedures')]
final class ProcedureController extends AbstractController
{
    #[Route('/', name: 'index_procedure',methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return all procedures',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Ok",
                "code"=>200,
                "message" => "Return all procedures",
                "data"=>[
                    [
                        'id'=>1,
                        'title'=>'Электрокардиография',
                        'description'=>'Взять его'
                    ],
                    [
                        'id'=>1,
                        'title'=>'Электрокардиография',
                        'description'=>'Взять его'
                    ],
                ]
            ]
        ),
    )]
    #[OA\Tag(name:"Procedure")]
    public function index(ProceduresService $proceduresService): JsonResponse
    {
        return $this->json($proceduresService->all());
    }
    #[Route('/{id}', name: 'show_procedure',methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return procedure info',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Ok",
                "code"=>200,
                "message" => "Return procedure info",
                "data"=>
                [
                    'id'=>1,
                    'title'=>'Электрокардиография',
                    'description'=>'Взять его',
                    "entityList" => [
                        [
                            "queue"=>3,
                            "status" => true,
                            "sourceId" => 2,
                            "sourceType" => "chambers"
                        ],
                        [
                            "queue"=>3,
                            "status" => true,
                            "sourceId" => 2,
                            "sourceType" => "chambers"
                        ],
                        [
                            "queue"=>3,
                            "status" => true,
                            "sourceId" => 2,
                            "sourceType" => "chambers"
                        ],
                    ]
                ],
            ]
        ),
    )]
    #[OA\Response(
        response: 404,
        description: 'Procedure not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not found",
                "code"=>404,
                "message" => "Procedure not found",


            ]
        ),
    )]
    #[OA\Tag(name:"Procedure")]
    public function show(ProceduresService $proceduresService,$id): JsonResponse
    {
        $response = $proceduresService->about($id);
        return $this->json($response,$response['code']);
    }
    #[Route( name: 'store_procedure',methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                type: 'object',
            ),
            example: [
                "title"=>"test",
                "description"=>"test"
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Created',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Created",
                "code"=>200,
                "message" => "Procedure has been create",
                "data" => [
                    "id"=>1,
                    "title"=>"test",
                    "description" => "test"
                ]
            ]
        ),
    )]
    #[OA\Response(
        response: 422,
        description: 'Check fields',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Error",
                "code"=>422,
                "message" => "Check your fields",
            ]
        ),
    )]
    #[OA\Response(
        response: 409,
        description: 'Conflict',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Conflict",
                "code"=>409,
                "message" => "Title is exists",
                "data"=>[
                    "id"=>19,
                    "title"=>'title',
                    "description" => "desc"
                ]
            ]
        ),
    )]
    #[OA\Tag(name:"Procedure")]
    public function store(Request $request,ProceduresService $proceduresService): JsonResponse
    {
        $response = $proceduresService->store($request->getContent());
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}', name: 'update_procedure',methods: ['PATCH'])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                type: 'object',
            ),
            example: [
                "title"=>"Test",
                "description"=>"Test desc"
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Procedure not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not found",
                "code"=>404,
                "message" => "Procedure not found",


            ]
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Procedure has been updated',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Update",
                "code"=>200,
                "message" => "Procedure has been updated",
                "data" => [
                    "id" => 1,
                    "title" => "title",
                    "description" => "Description"
                ]
            ]
        ),
    )]
    #[OA\Tag(name:"Procedure")]
    public function update(Request $request,ProceduresService $proceduresService,$id): JsonResponse
    {
        $response = $proceduresService->update($id,$request->getContent());
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}', name: 'delete_procedure',methods: ['DELETE'])]
    #[OA\Response(
        response: 404,
        description: 'Procedure not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not found",
                "code"=>404,
                "message" => "Procedure not found",


            ]
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Delete',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Delete",
                "code"=>200,
                "message" => "Procedure has been delete",
            ]
        ),
    )]
    #[OA\Tag(name:"Procedure")]
    public function delete(ProceduresService $proceduresService,$id): JsonResponse
    {
        $response = $proceduresService->delete($id);
        return $this->json($response,$response['code']);
    }
}
