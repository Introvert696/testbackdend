<?php

namespace App\Controller;

use App\Entity\Chambers;
use App\Services\ChambersService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class ChamberController extends AbstractController
{
    public function __construct(
        private readonly ChambersService $chambersService
    )
    {}
    #[Route('/chambers', name: 'index_chambers', methods: ["GET"])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $chambersRepository = $em->getRepository(Chambers::class);
        return $this->json($chambersRepository->findAll());
    }
    #[Route('/chambers/{id}', name: 'show_chambers', methods: ["GET"])]
    public function show(int $id): JsonResponse
    {
       $response = $this->chambersService->get($id);

       return $this->json($response);
    }
    #[Route('/chambers/{id}/procedures', name: 'show_chambers_procedures', methods: ["GET"])]
    public function showProcedures(int $id): JsonResponse
    {
        $response = $this->chambersService->getProcedure($id);
        return $this->json($response);
    }
    #[Route('/chambers/{id}/procedures', name: 'update_chambers_procedures', methods: ["POST"])]
    public function updateProcedures(Request $request,int $id): JsonResponse
    {
        $response = $this->chambersService->addProcedure($id,$request->getContent());
        return $this->json($response);
    }
}
