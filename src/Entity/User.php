<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Course;
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

    /**
     * @var Collection<int, CourseCompletion>
     */
    #[ORM\OneToMany(mappedBy: 'student', targetEntity: CourseCompletion::class, orphanRemoval: true)]
    private Collection $courseEnrollments;

    public function __construct()
    {
        $this->courseEnrollments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
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

    public function getCourses(): Collection
    {
        return $this->getCourseEnrollments();
    }
    
    /**
     * @return Collection<int, CourseCompletion>
     */
    public function getCourseEnrollments(): Collection
    {
        return $this->courseEnrollments;
    }

    public function enrollInCourse(Course $course): CourseCompletion
    {
        $enrollment = new CourseCompletion();
        $enrollment->setStudent($this);
        $enrollment->setCourse($course);
        $this->courseEnrollments->add($enrollment);
        return $enrollment;
    }

    public function isEnrolledInCourse(Course $course): bool
    {
        foreach ($this->courseEnrollments as $enrollment) {
            if ($enrollment->getCourse() === $course) {
                return true;
            }
        }
        return false;
    }

    public function hasCompletedCourse(Course $course): bool
    {
        foreach ($this->courseEnrollments as $enrollment) {
            if ($enrollment->getCourse() === $course && $enrollment->isCompleted()) {
                return true;
            }
        }
        return false;
    }

    public function getEnrollmentForCourse(Course $course): ?CourseCompletion
    {
        foreach ($this->courseEnrollments as $enrollment) {
            if ($enrollment->getCourse() === $course) {
                return $enrollment;
            }
        }
        return null;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getFullName(): string
    {
        return trim($this->name);
    }

    /**
     * @deprecated Use enrollInCourse() instead
     */
    public function addCourse(Course $course): self
    {
        if (!$this->isEnrolledInCourse($course)) {
            $this->enrollInCourse($course);
        }

        return $this;
    }

    /**
     * @deprecated Use CourseCompletion methods instead
     */
    public function removeCourse(Course $course): self
    {
        foreach ($this->courseEnrollments as $enrollment) {
            if ($enrollment->getCourse() === $course) {
                $this->courseEnrollments->removeElement($enrollment);
                break;
            }
        }

        return $this;
    }
}
