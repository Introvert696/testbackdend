<?php

namespace App\Controller;

use App\Entity\Patients;
use App\Services\PatientsServices;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients')]
final class PatientController extends AbstractController
{
    public function __construct(
        private readonly PatientsServices $patientsServices
    ){}
    #[Route('/',name:'index_patients',methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->patientsServices->all());
    }
    #[Route('/{id}', name: 'show_patients', defaults: ['id'=>null], methods: ['GET'])]
    public function get(EntityManagerInterface $em,int|null $id): JsonResponse
    {
        // card_number and cardNumber - fix it
        return $this->json($this->patientsServices->about($id));
    }
    #[Route(name:'store_patients',methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $response = $this->patientsServices->createOrFind($request->getContent());
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}', name: 'update_patients', defaults: ['id'=>null], methods: ['PUT'])]
    public function update(Request $request,int|null $id): JsonResponse
    {
        $response = $this->patientsServices->update($id,$request->getContent());
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}', name: 'delete_patients', defaults: ['id'=>null], methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em,int|null $id): JsonResponse
    {
        $response = $this->patientsServices->remove($id);
        return $this->json($response,$response['code']);
    }

}
