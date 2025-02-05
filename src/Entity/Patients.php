<?php

namespace App\Entity;

use App\Repository\PatientsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: PatientsRepository::class)]
class Patients
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: "card_number",type: 'integer', unique: true)]
    private ?int $card_number = null;

    #[Ignore]
    #[ORM\OneToOne(mappedBy: 'patients', cascade: ['persist', 'remove'])]
    private ?ChambersPatients $chambersPatients = null;


    public function __construct()
    {
        $this->procedureLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCardNumber(): ?int
    {
        return $this->card_number;
    }

    public function setCardNumber(int $cardNumber): static
    {
        $this->card_number = $cardNumber;

        return $this;
    }

    public function getChambersPatients(): ?ChambersPatients
    {
        return $this->chambersPatients;
    }

    public function setChambersPatients(ChambersPatients $chambersPatients): static
    {
        // set the owning side of the relation if necessary
        if ($chambersPatients->getPatients() !== $this) {
            $chambersPatients->setPatients($this);
        }

        $this->chambersPatients = $chambersPatients;

        return $this;
    }
}
