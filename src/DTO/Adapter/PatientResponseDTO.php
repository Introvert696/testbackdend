<?php

namespace App\DTO\Adapter;

class PatientResponseDTO
{
    public ?int $id= null;
    public ?string $name= null;
    public ?int $card_number= null;
    public ?int $chamber = null;
    public ?array $procedures ;

    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
    public function getCard_number(): int
    {
        return $this->card_number;
    }

    public function setCardNumber(int $card_number): static
    {
        $this->card_number = $card_number;

        return $this;
    }
    public function getChamber(): int
    {
        return $this->chamber;
    }
    public function setChamber(int $chamber): static
    {
        $this->chamber = $chamber;

        return $this;
    }
    public function getProcedures(): ?array
    {
        return $this->procedures;
    }
    public function setProcedures(?array $procedures): static
    {
        $this->procedures = $procedures;

        return $this;
    }
    public function addProc($proc): static
    {
        $this->procedures[] = $proc;
        return $this;
    }



}