<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class of differents screens of builds
 */
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[Vich\Uploadable]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[Vich\UploadableField(mapping: 'screens', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;
    
    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;
    
    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;
    
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // Case if the image references imageBuild
    #[ORM\OneToOne(mappedBy: 'imageBuild', cascade: ['persist'])]
    private ?Tutorial $tutorial = null;

    // Case if the image references a step
    #[ORM\ManyToOne(inversedBy: 'steps')]
    private ?Tutorial $tutorialSteps = null;
    
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
        
        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
    
    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }
    
    public function getImageName(): ?string
    {
        return $this->imageName;
    }
    
    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }
    
    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(\DateTimeImmutable $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getTutorial(): ?Tutorial
    {
        return $this->tutorial;
    }

    public function setTutorial(Tutorial $tutorial): static
    {
        // set the owning side of the relation if necessary
        if ($tutorial->getImageBuild() !== $this) {
            $tutorial->setImageBuild($this);
        }

        $this->tutorial = $tutorial;

        return $this;
    }
    
    public function __toString()
    {
        return $this->imageName;
    }

    public function getTutorialSteps(): ?Tutorial
    {
        return $this->tutorialSteps;
    }

    public function setTutorialSteps(?Tutorial $tutorialSteps): static
    {
        $this->tutorialSteps = $tutorialSteps;

        return $this;
    }
}
