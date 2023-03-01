<?php

namespace App\Entity;

use App\Repository\FundrisingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: FundrisingRepository::class)]
class Fundrising
{
  
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[Assert\NotBlank(message:"Title fundrising is required")]
    #[ORM\Column(length: 255)]
    private ?string $TitreDon = null;
    #[Assert\NotBlank(message:"description fundrising is required")]
    #[ORM\Column(length: 255)]
    private ?string $descriptionDon = null;

    #[ORM\Column(length: 255)]
    private ?string $imageDon = null;
    #[Assert\NotBlank(message:"date fundrising is required")]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_Don = null;
    #[Assert\NotBlank(message:"date limit  fundrising is required")]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_don_limite = null;
    #[ORM\Column(length: 255)]
    private ?string $etat = null;
    #[Assert\NotBlank(message:"Objectif fundrising is required")]
    #[ORM\Column]
    private ?float $objectif = null;

    #[ORM\OneToMany(mappedBy: 'fundsID', targetEntity: DonHistory::class)]
    private Collection $donHistories;

    public function __construct()
    {
        $this->donHistories = new ArrayCollection();
    }




 

   
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

    /**
     * @return Collection<int, DonHistory>
     */
    public function getDonHistories(): Collection
    {
        return $this->donHistories;
    }

    public function addDonHistory(DonHistory $donHistory): self
    {
        if (!$this->donHistories->contains($donHistory)) {
            $this->donHistories->add($donHistory);
            $donHistory->setFundsID($this);
        }

        return $this;
    }

    public function removeDonHistory(DonHistory $donHistory): self
    {
        if ($this->donHistories->removeElement($donHistory)) {
            // set the owning side to null (unless already changed)
            if ($donHistory->getFundsID() === $this) {
                $donHistory->setFundsID(null);
            }
        }

        return $this;
    }

 
   
  
}
