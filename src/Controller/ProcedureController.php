<?php

namespace App\Controller;

use App\Services\ProceduresService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/procedures')]
final class ProcedureController extends AbstractController
{
    #[Route('/', name: 'index_procedure',methods: ['GET'])]
    public function index(ProceduresService $proceduresService): JsonResponse
    {
        return $this->json($proceduresService->all());
    }
    #[Route('/{id}', name: 'show_procedure',methods: ['GET'])]
    public function show(ProceduresService $proceduresService,$id): JsonResponse
    {
        return $this->json($proceduresService->about($id));
    }
    #[Route( name: 'store_procedure',methods: ['POST'])]
    public function store(Request $request,ProceduresService $proceduresService): JsonResponse
    {
        $response = $proceduresService->store($request->getContent());
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}', name: 'update_procedure',methods: ['PUT'])]
    public function update(Request $request,ProceduresService $proceduresService,$id): JsonResponse
    {
        $response = $proceduresService->update($id,$request->getContent());
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}', name: 'delete_procedure',methods: ['Delete'])]
    public function delete(ProceduresService $proceduresService,$id): JsonResponse
    {
        return $this->json($proceduresService->delete($id));
    }
}
