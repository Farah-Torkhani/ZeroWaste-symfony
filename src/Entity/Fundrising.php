<?php

namespace App\Entity;

use App\Repository\FundrisingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FundrisingRepository::class)]
class Fundrising
{
  
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $TitreDon = null;

    #[ORM\Column(length: 255)]
    private ?string $descriptionDon = null;

    #[ORM\Column(length: 255)]
    private ?string $imageDon = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_Don = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_don_limite = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\Column]
    private ?float $objectif = null;

    #[ORM\ManyToOne(inversedBy: 'fundrisings_id')]
    private ?DonHistory $donHistory = null;




 

   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitreDon(): ?string
    {
        return $this->TitreDon;
    }

    public function setTitreDon(string $TitreDon): self
    {
        $this->TitreDon = $TitreDon;

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
        return $this->date_Don;
    }

    public function setDateDon(\DateTimeInterface $date_Don): self
    {
        $this->date_Don = $date_Don;

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

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
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

    public function getDonHistory(): ?DonHistory
    {
        return $this->donHistory;
    }

    public function setDonHistory(?DonHistory $donHistory): self
    {
        $this->donHistory = $donHistory;

        return $this;
    }


   
  
}
