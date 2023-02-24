<?php

namespace App\Entity;

use App\Repository\CommandesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandesRepository::class)]
class Commandes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantiteC = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_ajout = null;

    #[ORM\Column]
    private ?int $checkOut = null;

    #[ORM\ManyToOne]
    private ?Produit $produit_id = null;

    #[ORM\ManyToOne]
    private ?User $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantiteC(): ?int
    {
        return $this->quantiteC;
    }

    public function setQuantiteC(int $quantiteC): self
    {
        $this->quantiteC = $quantiteC;

        return $this;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->date_ajout;
    }

    public function setDateAjout(\DateTimeInterface $date_ajout): self
    {
        $this->date_ajout = $date_ajout;

        return $this;
    }

    public function getCheckOut(): ?int
    {
        return $this->checkOut;
    }

    public function setCheckOut(int $checkOut): self
    {
        $this->checkOut = $checkOut;

        return $this;
    }

    public function getProduitId(): ?Produit
    {
        return $this->produit_id;
    }

    public function setProduitId(?Produit $produit_id): self
    {
        $this->produit_id = $produit_id;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    
}
