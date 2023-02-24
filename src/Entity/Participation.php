<?php

namespace App\Entity;

use App\Repository\ParticipationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $verification_p = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $phone = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToMany(targetEntity: Collecte::class, inversedBy: 'participations')]
    private Collection $collecte_id;

    public function __construct()
    {
        $this->collecte_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVerificationP(): ?int
    {
        return $this->verification_p;
    }

    public function setVerificationP(int $verification_p): self
    {
        $this->verification_p = $verification_p;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): self
    {
        $this->phone = $phone;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Collecte>
     */
    public function getCollecteId(): Collection
    {
        return $this->collecte_id;
    }
    
    public function setCollecteId(int $collecte_id): self
    {
        $this->collecte_id = $collecte_id;

        return $this;
    }
    
    public function addCollecteId(Collecte $collecteId): self
    {
        if (!$this->collecte_id->contains($collecteId)) {
            $this->collecte_id->add($collecteId);
        }

        return $this;
    }

    public function removeCollecteId(Collecte $collecteId): self
    {
        $this->collecte_id->removeElement($collecteId);

        return $this;
    }
}
