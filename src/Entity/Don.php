<?php

namespace App\Entity;

use App\Repository\DonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DonRepository::class)]
class Don
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titreDon = null;

    #[ORM\Column(length: 255)]
    private ?string $descriptionDon = null;

    #[ORM\Column(length: 255)]
    private ?string $imageDon = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_don = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_don_limite = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column]
    private ?float $objectif = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreDon(): ?string
    {
        return $this->titreDon;
    }

    public function setTitreDon(string $titreDon): self
    {
        $this->titreDon = $titreDon;

        return $this;
    }

    public function getDescriptionDon(): ?string
    {
        return $this->descriptionDon;
    }

    public function setDescriptionDon(string $descriptionDon): self
    {
        $this->descriptionDon = $descriptionDon;

        return $this;
    }

    public function getImageDon(): ?string
    {
        return $this->imageDon;
    }

    public function setImageDon(string $imageDon): self
    {
        $this->imageDon = $imageDon;

        return $this;
    }

    public function getDateDon(): ?\DateTimeInterface
    {
        return $this->date_don;
    }

    public function setDateDon(\DateTimeInterface $date_don): self
    {
        $this->date_don = $date_don;

        return $this;
    }

    public function getDateDonLimite(): ?\DateTimeInterface
    {
        return $this->date_don_limite;
    }

    public function setDateDonLimite(\DateTimeInterface $date_don_limite): self
    {
        $this->date_don_limite = $date_don_limite;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getObjectif(): ?float
    {
        return $this->objectif;
    }

    public function setObjectif(float $objectif): self
    {
        $this->objectif = $objectif;

        return $this;
    }
}
