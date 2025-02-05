<?php

class ChamberResponse
{
    public ?int $id=null;
    public ?int $number=null;
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