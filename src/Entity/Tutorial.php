<?php

namespace App\Entity;

use App\Repository\TutorialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class that represents a build, its features, and the steps to build it
 */
#[ORM\Entity(repositoryClass: TutorialRepository::class)]
class Tutorial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'tutorials')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Library $library = null;

    /**
     * @var Collection<int, Theme>
     */
    #[ORM\ManyToMany(targetEntity: Theme::class, mappedBy: 'tutorials', cascade: ['persist'])]
    private Collection $themes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    // The image that will be showed to present the build
    #[ORM\OneToOne(inversedBy: 'tutorial', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Image $imageBuild = null;

    
    // The images of each step, in lexicographic order
    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'tutorialSteps')]
    #[ORM\OrderBy(['imageName' => 'ASC'])]
    private Collection $steps;

    public function __construct()
    {
        $this->themes = new ArrayCollection();
        $this->steps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLibrary(): ?Library
    {
        return $this->library;
    }

    public function setLibrary(?Library $library): static
    {
        $this->library = $library;

        return $this;
    }

    /**
     * @return Collection<int, TutorialSet>
     */
    public function getTheme(): Collection
    {
        return $this->themes;
    }

    public function addTheme(Theme $theme): static
    {
        if (!$this->themes->contains($theme)) {
            $this->themes->add($theme);
            $theme->addTutorial($this);
        }

        return $this;
    }

    public function removeTutorialSet(Theme $theme): static
    {
        if ($this->themes->removeElement($theme)) {
            $theme->removeTutorial($this);
        }

        return $this;
    }
    
    public function __toString(): string
    {
        return $this->name;
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

    public function getImageBuild(): ?Image
    {
        return $this->imageBuild;
    }

    public function setImageBuild(Image $imageBuild): static
    {
        $this->imageBuild = $imageBuild;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Image $step): static
    {
        if (!$this->steps->contains($step)) {
            $this->steps->add($step);
            $step->setTutorialSteps($this);
        }

        return $this;
    }

    public function removeStep(Image $step): static
    {
        if ($this->steps->removeElement($step)) {
            // set the owning side to null (unless already changed)
            if ($step->getTutorialSteps() === $this) {
                $step->setTutorialSteps(null);
            }
        }

        return $this;
    }
}
