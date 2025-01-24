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
    #[Route('/patient',name:'index_patient',methods: ['GET'])]
    public function index(): JsonResponse
    {
        $response = $this->patientsServices->all();
        return $this->json($response);
    }
    #[Route('/patient',name:'store_patient',methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $result = $this->patientsServices->createOrFind($request->getContent());
        return $this->json($result);
    }
    #[Route('/patient/{id}', name: 'show_patient', defaults: ['id'=>null], methods: ['GET'])]
    public function get(EntityManagerInterface $em,int|null $id): JsonResponse
    {
        $patientsRepository = $em->getRepository(Patients::class);
        $patient = $patientsRepository->get($id);
        return $this->json($patient);
    }

    #[Route('/patient/{id}', name: 'update_patient', defaults: ['id'=>null], methods: ['PUT'])]
    public function update(Request $request,int|null $id): JsonResponse
    {
        $result = $this->patientsServices->update($id,$request->getContent());
        return $this->json($result);
    }
    #[Route('/patient/{id}', name: 'delete_patient', defaults: ['id'=>null], methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em,int|null $id): JsonResponse
    {
        $patientsRepository = $em->getRepository(Patients::class);
        $patient = $patientsRepository->get($id);
        return $this->json($patient);
    }

}
