<?php

namespace App\DTO;


class ChamberDTO
{
    public int $procedure_id;
    public int $queue;
    public int $patient_id;

    public function getProcedureId(): int
    {
        return $this->procedure_id;
    }


    public function setProcedureId(int $procedure_id): static
    {
        $this->procedure_id = $procedure_id;

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

    public function getPatientId(): int
    {
        return $this->patient_id;
    }


    public function setPatientId(int $patient_id): static
    {
        $this->patient_id = $patient_id;

        return $this;
    }
}