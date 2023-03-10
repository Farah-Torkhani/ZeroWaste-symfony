<?php

namespace App\Entity;

use App\Repository\CollecteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;


#[UniqueEntity(fields: ['nomCollecte'], message: 'Ce nom de catégorie est déjà utilisé.')]
#[ORM\Entity(repositoryClass: CollecteRepository::class)]
class Collecte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("produit_group")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"nom est vide") ]
    #[Groups("produit_group")]  
    private ?string $nomCollecte = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"description est vide") ] 
    #[Groups("produit_group")] 
    private ?string $description = null;

    #[ORM\Column(length: 255)] 
    #[Groups("produit_group")]
    private ?string $imageCollect = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"etat est vide") ]  
    #[Groups("produit_group")]
    private ?int $etat = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"adresse est vide") ]  
    #[Groups("produit_group")]
    private ?string $adresse = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups("produit_group")]
    private ?\DateTimeInterface $date_deb = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]  
    #[Groups("produit_group")]
    private ?\DateTimeInterface $date_fin = null;

     /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->date_deb && $this->date_fin && $this->date_deb >= $this->date_fin) {
            $context->buildViolation('La date de début doit être inférieure à la date de fin.')
                ->atPath('date_deb')
                ->addViolation();
        }
    }

    #[ORM\ManyToOne(inversedBy: 'collect_id')]
    private ?CollecteCategorie $collecteCategorie = null;

    #[ORM\ManyToMany(targetEntity: Participation::class, mappedBy: 'collecte_id')]
    private Collection $participations;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->addCollecteId($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->removeElement($participation)) {
            $participation->removeCollecteId($this);
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getNomCollecte();
    }
}
