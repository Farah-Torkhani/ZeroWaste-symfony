<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "le champ est vide")]
    private ?string $nomProduit = null;


    #[ORM\Column]
    #[Assert\NotBlank(message: "le champ est vide")]
    #[Assert\Positive]
    private ?int $quantite = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "le champ est vide")]
    #[Assert\Positive]
    private ?float $prixProduit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Image = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "le champ est vide")]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'produit_id')]
    private ?CategorieProduit $categorieProduit = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive]
    #[Assert\NotBlank(message: "le champ est vide")]
    private ?int $prix_point_produit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit): self
    {
        $this->nomProduit = $nomProduit;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixProduit(): ?float
    {
        return $this->prixProduit;
    }

    public function setPrixProduit(float $prixProduit): self
    {
        $this->prixProduit = $prixProduit;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(string $Image): self
    {
        $this->Image = $Image;

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

    public function getCategorieProduit(): ?CategorieProduit
    {
        return $this->categorieProduit;
    }

    public function setCategorieProduit(?CategorieProduit $categorieProduit): self
    {
        $this->categorieProduit = $categorieProduit;

        return $this;
    }

    public function getPrixPointProduit(): ?int
    {
        return $this->prix_point_produit;
    }

    public function setPrixPointProduit(?int $prix_point_produit): self
    {
        $this->prix_point_produit = $prix_point_produit;

        return $this;
    }
}
