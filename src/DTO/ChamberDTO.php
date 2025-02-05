<?php

namespace App\DTO;

class ChamberDTO
{
    public int|string|null $number = null;

    public function setNumber(string|int $number): static
    {
        if(is_int($number)){
            $this->number = $number;
        }
        else{
            $this->number = null;
        }

        return $this;
    }
    public function getNumber(): int|null
    {
        return $this->number;
    }
}

