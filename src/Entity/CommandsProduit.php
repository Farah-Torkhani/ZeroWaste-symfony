<?php

namespace App\Entity;

use App\Repository\CommandsProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandsProduitRepository::class)]
class CommandsProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantiteC = null;

    #[ORM\ManyToOne]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(inversedBy: 'commandsProduits')]
    private ?Commands $commande = null;

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

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getCommande(): ?Commands
    {
        return $this->commande;
    }

    public function setCommande(?Commands $commande): self
    {
        $this->commande = $commande;

        return $this;
    }
}
