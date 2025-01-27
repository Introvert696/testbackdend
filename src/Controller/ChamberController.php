<?php

namespace App\Controller;

use App\Entity\Chambers;
use App\Services\ChambersService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class ChamberController extends AbstractController
{
    public function __construct(
        private readonly ChambersService $chambersService
    )
    {}

    #[Route('/chambers', name: 'index_chamber', methods: ["GET"])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $chambersRepository = $em->getRepository(Chambers::class);
        return $this->json($chambersRepository->findAll());
    }
    #[Route('/chambers/{id}', name: 'show_chamber', methods: ["GET"])]
    public function show(int $id): JsonResponse
    {
       $response = $this->chambersService->get($id);

       return $this->json($response);
    }
    #[Route('/chambers/{id}/procedures', name: 'show_chamber_procedures', methods: ["GET"])]
    public function showProcedures(int $id): JsonResponse
    {
        // получить информацию о процедурах для конкретной палаты
        $response = $this->chambersService->getProcedure($id);
        return $this->json($response);
    }
}
