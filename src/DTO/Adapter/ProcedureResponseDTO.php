<?php

namespace App\DTO;

class ProcedureResponseDTO
{
    public int $id;
    public string $title;
    public string $description;
    public array $entityList;

    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function addEntity(object $entity): static
    {
        $this->entityList[] = $entity;
        return $this;
    }


}