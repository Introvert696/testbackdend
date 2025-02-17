<?php

namespace App\Entity;

use App\Repository\ChambersPatientsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: ChambersPatientsRepository::class)]
class ChambersPatients
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne( inversedBy: 'chambersPatients')]
    private ?Chambers $chambers = null;

    #[ORM\OneToOne(targetEntity: Patients::class, inversedBy: 'chambersPatients')]
    #[ORM\JoinColumn(name:'patients_id',referencedColumnName: 'id',nullable: false,onDelete: 'CASCADE')]
    #[Ignore]
    private ?Patients $patients = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChambers(): ?Chambers
    {
        return $this->chambers;
    }

    public function setChambers(?Chambers $chambers): static
    {
        $this->chambers = $chambers;

        return $this;
    }

    public function getPatients(): ?Patients
    {
        return $this->patients;
    }

    public function setPatients(Patients $patients): static
    {
        $this->patients = $patients;

        return $this;
    }
}
