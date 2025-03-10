<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[Vich\Uploadable]
class Course
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $vidoeEmbed = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $day = null;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'course_image', fileNameProperty: 'image')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private ?bool $locked = false;

    #[Vich\UploadableField(mapping: 'course_certificate', fileNameProperty: 'certificateTemplate')]
    private ?File $certificateTemplateFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $certificateTemplate = null;

    /**
     * @var Collection<int, CourseCompletion>
     */
    #[ORM\OneToMany(mappedBy: 'course', targetEntity: CourseCompletion::class, orphanRemoval: true)]
    private Collection $studentEnrollments;

    public function __construct()
    {
        $this->studentEnrollments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getVidoeEmbed(): ?string
    {
        return $this->vidoeEmbed;
    }

    public function setVidoeEmbed(string $vidoeEmbed): static
    {
        $this->vidoeEmbed = $vidoeEmbed;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(?int $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function isLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): static
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $certificateTemplateFile
     */
    public function setCertificateTemplateFile(?File $certificateTemplateFile = null): void
    {
        $this->certificateTemplateFile = $certificateTemplateFile;

        if (null !== $certificateTemplateFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getCertificateTemplateFile(): ?File
    {
        return $this->certificateTemplateFile;
    }

    public function getCertificateTemplate(): ?string
    {
        return $this->certificateTemplate;
    }

    public function setCertificateTemplate(?string $certificateTemplate): static
    {
        $this->certificateTemplate = $certificateTemplate;
        return $this;
    }

    /**
     * @return Collection<int, CourseCompletion>
     */
    public function getStudentEnrollments(): Collection
    {
        return $this->studentEnrollments;
    }

    public function isStudentEnrolled(User $user): bool
    {
        foreach ($this->studentEnrollments as $enrollment) {
            if ($enrollment->getStudent() === $user) {
                return true;
            }
        }
        return false;
    }

    public function addStudent(User $student): CourseCompletion
    {
        return $student->enrollInCourse($this);
    }

    public function isCompletedByUser(User $user): bool
    {
        foreach ($this->studentEnrollments as $enrollment) {
            if ($enrollment->getStudent() === $user && $enrollment->isCompleted()) {
                return true;
            }
        }
        return false;
    }

    public function removeStudent(User $student): static
    {
        $enrollment = $student->getEnrollmentForCourse($this);
        if ($enrollment) {
            $this->studentEnrollments->removeElement($enrollment);
            // If using doctrine cascade remove, this isn't necessary
            // $student->getCourseEnrollments()->removeElement($enrollment);
        }

        return $this;
    }
}
