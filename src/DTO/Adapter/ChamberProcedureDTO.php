<?php

namespace App\DTO\Adapter;
class ChamberProcedureDTO
{
    public int $id;
    public string $title;
    public string $desc;
    public int $queue;
    public bool $status;


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

    public function getDesc(): string
    {
        return $this->desc;
    }

    public function setDesc(string $desc): static
    {
        $this->desc = $desc;

        return $this;
    }

    public function getQueue(): int
    {
        return $this->queue;
    }

    public function setQueue(int $queue): static
    {
        $this->queue = $queue;

        return $this;
    }

    public function isStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }
}
