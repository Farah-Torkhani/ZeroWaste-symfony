<?php

namespace App\Entity;

use App\Repository\CollecteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollecteRepository::class)]
class Collecte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomCollecte = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $imageCollect = null;

    #[ORM\Column]
    private ?int $etat = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_deb = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\ManyToOne(inversedBy: 'collecte_id')]
    private ?CollecteCategorie $collecteCategorie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCollecte(): ?string
    {
        return $this->nomCollecte;
    }

    public function setNomCollecte(string $nomCollecte): self
    {
        $this->nomCollecte = $nomCollecte;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImageCollect(): ?string
    {
        return $this->imageCollect;
    }

    public function setImageCollect(string $imageCollect): self
    {
        $this->imageCollect = $imageCollect;

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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDateDeb(): ?\DateTimeInterface
    {
        return $this->date_deb;
    }

    public function setDateDeb(\DateTimeInterface $date_deb): self
    {
        $this->date_deb = $date_deb;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getCollecteCategorie(): ?CollecteCategorie
    {
        return $this->collecteCategorie;
    }

    public function setCollecteCategorie(?CollecteCategorie $collecteCategorie): self
    {
        $this->collecteCategorie = $collecteCategorie;

        return $this;
    }
}
