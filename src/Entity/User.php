<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Your name cannot contain a number',
    )]
    private ?string $fullName = null;


    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\Regex(
        pattern: '/^[0-9]{8}\d*$/',
        match: true,
        message: 'not a valid phone number',
    )]
    #[Assert\NotBlank]
    private ?string $tel = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: "boolean", options: ['default' => false])]
    private ?bool $isVerified = false;

    #[ORM\Column(type: "boolean", options: ['default' => true])]
    private ?bool $state = true;

    #[ORM\Column(nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $fbLink = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $twitterLink = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $instaLink = null;

    #[ORM\Column(length: 180, nullable: true, options: ['default' => 'defaultPic.jpg'])]
    private ?string $imgUrl = null;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private ?int $point = 0;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 8,
        minMessage: 'Your password must be at least {{ limit }} characters long',
    )]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ProductFavoris::class)]
    private Collection $productFavoris;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserNotification::class)]
    private Collection $userNotifications;

    public function __construct()
    {
        $this->productFavoris = new ArrayCollection();
        $this->userNotifications = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoint(): ?int
    {
        return $this->point;
    }

    public function setPoint(int $point): self
    {
        $this->point = $point;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

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

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }


    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;

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


    public function getFbLink(): ?string
    {
        return $this->fbLink;
    }

    public function setFbLink(string $fbLink): self
    {
        $this->fbLink = $fbLink;

        return $this;
    }

    public function getTwitterLink(): ?string
    {
        return $this->twitterLink;
    }

    public function setTwitterLink(string $twitterLink): self
    {
        $this->twitterLink = $twitterLink;

        return $this;
    }

    public function getInstaLink(): ?string
    {
        return $this->instaLink;
    }

    public function setInstaLink(string $instaLink): self
    {
        $this->instaLink = $instaLink;

        return $this;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(string $imgUrl): self
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }



    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * @return Collection<int, ProductFavoris>
     */
    public function getProductFavoris(): Collection
    {
        return $this->productFavoris;
    }

    public function addProductFavori(ProductFavoris $productFavori): self
    {
        if (!$this->productFavoris->contains($productFavori)) {
            $this->productFavoris->add($productFavori);
            $productFavori->setUser($this);
        }

        return $this;
    }

    public function removeProductFavori(ProductFavoris $productFavori): self
    {
        if ($this->productFavoris->removeElement($productFavori)) {
            // set the owning side to null (unless already changed)
            if ($productFavori->getUser() === $this) {
                $productFavori->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserNotification>
     */
    public function getUserNotifications(): Collection
    {
        return $this->userNotifications;
    }

    public function addUserNotification(UserNotification $userNotification): self
    {
        if (!$this->userNotifications->contains($userNotification)) {
            $this->userNotifications->add($userNotification);
            $userNotification->setUser($this);
        }

        return $this;
    }

    public function removeUserNotification(UserNotification $userNotification): self
    {
        if ($this->userNotifications->removeElement($userNotification)) {
            // set the owning side to null (unless already changed)
            if ($userNotification->getUser() === $this) {
                $userNotification->setUser(null);
            }
        }

        return $this;
    }
    

    
}
