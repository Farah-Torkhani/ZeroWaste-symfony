<?php

namespace App\Entity;

use App\Repository\AchatsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AchatsRepository::class)]
class Achats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAchat = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $FullName = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    #[Assert\NotBlank]
    private ?string $Email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $Address = null;

    #[ORM\Column]
    #[Assert\Regex(
        pattern: '/^[0-9]{8}\d*$/',
        match: true,
        message: 'not a valid phone number',
    )]
    #[Assert\NotBlank]
    private ?int $tel = null;

    #[ORM\ManyToOne]
    private ?Commands $commande = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $city = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Positive()]
    private ?int $zipCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paymentMethod = null;

    #[ORM\Column]
    private ?int $validate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->dateAchat;
    }

    public function setDateAchat(\DateTimeInterface $dateAchat): self
    {
        $this->dateAchat = $dateAchat;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->FullName;
    }

    public function setFullName(string $FullName): self
    {
        $this->FullName = $FullName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->Address;
    }

    public function setAddress(string $Address): self
    {
        $this->Address = $Address;

        return $this;
    }

    public function getTel(): ?int
    {
        return $this->tel;
    }

    public function setTel(int $tel): self
    {
        $this->tel = $tel;

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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    public function setZipCode(int $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getValidate(): ?int
    {
        return $this->validate;
    }

    public function setValidate(int $validate): self
    {
        $this->validate = $validate;

        return $this;
    }
}
