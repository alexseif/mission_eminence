<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $referredBy = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $iGeniusUserID = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameOfEnroller = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getReferredBy(): ?string
    {
        return $this->referredBy;
    }

    public function setReferredBy(?string $referredBy): static
    {
        $this->referredBy = $referredBy;

        return $this;
    }

    public function getIGeniusUserID(): ?string
    {
        return $this->iGeniusUserID;
    }

    public function setIGeniusUserID(?string $iGeniusUserID): static
    {
        $this->iGeniusUserID = $iGeniusUserID;

        return $this;
    }

    public function getNameOfEnroller(): ?string
    {
        return $this->nameOfEnroller;
    }

    public function setNameOfEnroller(?string $nameOfEnroller): static
    {
        $this->nameOfEnroller = $nameOfEnroller;

        return $this;
    }

    public  function eraseCredentials() {}

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
