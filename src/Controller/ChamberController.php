<?php

namespace App\Controller;

use App\Entity\Chambers;
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
                type: 'array',
                items: new OA\Items(ref: new Model(type:Chambers::class, groups:['full']))
        )
    )]
    #[OA\Tag(name: 'index')]
    public function index(): JsonResponse
    {
        $response = $this->chambersService->all();
        return $this->json($response);
    }
    #[Route('/{id}', name: 'show_chambers', methods: ["GET"])]
    public function show(int $id): JsonResponse
    {
        $response = $this->chambersService->get($id);
       return $this->json($response,$response['code']);
    }
    #[Route('/{id}/procedures', name: 'show_chambers_procedures', methods: ["GET"])]
    public function showProcedures(int $id): JsonResponse
    {
        $response = $this->chambersService->getProcedure($id);
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}/procedures', name: 'update_chambers_procedures', methods: ["POST"])]
    public function updateProcedures(Request $request,int $id): JsonResponse
    {
        $response = $this->chambersService->addProcedure($id,$request->getContent());
        return $this->json($response,$response['code']);
    }
    #[Route(name: 'store_chambers', methods: ["POST"])]
    public function store(Request $request): JsonResponse
    {
        $response = $this->chambersService->create($request->getContent());
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}', name: 'update_chambers', methods: ["PATCH"])]
    public function update(Request $request,int|null $id): JsonResponse
    {
        $response = $this->chambersService->update($id,$request->getContent());
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}', name: 'delete_chambers', methods: ["DELETE"])]
    public function delete(int|null $id): JsonResponse
    {
        $response = $this->chambersService->delete($id);
        return $this->json($response,$response['code']);
    }
}
