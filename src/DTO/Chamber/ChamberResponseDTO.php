<?php

namespace App\DTO\Chamber;

use App\Entity\Patients;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class ChamberResponseDTO
{
    #[OA\Property(type: 'integer')]
    public int $id;
    #[OA\Property(type: 'integer')]
    public int $number;
    #[OA\Property(
        type: 'array',
        items: new OA\Items(
            ref: new Model(type:Patients::class)
        ),
        example: [
            [
                "id"=>1,
                "name"=>"FFF FFF FFF",
                "card_number"=>23423
            ],
            [
                "id"=>1,
                "name"=>"FFF FFF FFF",
                "card_number"=>23423
            ],
        ]
    )]

    public ?array $patients = [];

    public function getId(): ?int
    {
        return $this->id;
    }


    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getPatients(): ?array
    {
        return $this->patients;
    }

    public function setPatients(?array $patients): static
    {
        $this->patients = $patients;

        return $this;
    }
}