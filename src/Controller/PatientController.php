<?php

namespace App\Controller;

use App\Entity\Patients;
use App\Services\PatientsServices;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class PatientController extends AbstractController
{
    public function __construct(
        private readonly PatientsServices $patientsServices
    ){}
    #[Route('/patients',name:'index_patients',methods: ['GET'])]
    public function index(): JsonResponse
    {
        $response = $this->patientsServices->all();
        return $this->json($response,$response['code']);
    }
    #[Route('/patients',name:'store_patients',methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $result = $this->patientsServices->createOrFind($request->getContent());
        return $this->json($result,202);
    }
    #[Route('/patients/{id}', name: 'show_patients', defaults: ['id'=>null], methods: ['GET'])]
    public function get(EntityManagerInterface $em,int|null $id): JsonResponse
    {
        $response  = $this->patientsServices->about($id);
        // card_number and cardNumber - fix it
        return $this->json($response);
    }

    #[Route('/patients/{id}', name: 'update_patients', defaults: ['id'=>null], methods: ['PUT'])]
    public function update(Request $request,int|null $id): JsonResponse
    {
        $response = $this->patientsServices->update($id,$request->getContent());
        return $this->json($response,$response['code']);
    }
    #[Route('/patients/{id}', name: 'delete_patients', defaults: ['id'=>null], methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em,int|null $id): JsonResponse
    {
        $response = $this->patientsServices->remove($id);
        return $this->json($response,$response['code']);
    }

}
