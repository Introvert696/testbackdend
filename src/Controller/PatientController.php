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
    #[Route('/patient',name:'index_patient',methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $patientsRepository = $em->getRepository(Patients::class);
        $patients = $patientsRepository->findAll();
        return $this->json($patients);
    }
    #[Route('/patient',name:'store_patient',methods: ['POST'])]
    public function store(Request $request,PatientsServices $patientsServices): JsonResponse
    {
        $result = $patientsServices->createOrFind($request->getContent());
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
    public function update(Request $request,EntityManagerInterface $em,int|null $id): JsonResponse
    {
        dd('update');
        $patientsRepository = $em->getRepository(Patients::class);
        $patient = $patientsRepository->get($id);
        return $this->json($patient);
    }
    #[Route('/patient/{id}', name: 'delete_patient', defaults: ['id'=>null], methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em,int|null $id): JsonResponse
    {
        dd('delete');
        $patientsRepository = $em->getRepository(Patients::class);
        $patient = $patientsRepository->get($id);
        return $this->json($patient);
    }

}
