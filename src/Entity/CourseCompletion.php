<?php

namespace App\Entity;

use App\Repository\CourseCompletionRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: CourseCompletionRepository::class)]
#[ORM\Table(name: 'user_course')]
class CourseCompletion
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'courseEnrollments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $student = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'studentEnrollments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $certificatePath = null;

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(?User $student): static
    {
        $this->student = $student;
        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->completedAt !== null;
    }

    public function complete(): static
    {
        $this->completedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getCertificatePath(): ?string
    {
        return $this->certificatePath;
    }

    public function setCertificatePath(?string $certificatePath): static
    {
        $this->certificatePath = $certificatePath;
        return $this;
    }
} 