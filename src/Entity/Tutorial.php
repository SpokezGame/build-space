<?php

namespace App\Entity;

use App\Repository\TutorialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TutorialRepository::class)]
class Tutorial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[ORM\ManyToOne(inversedBy: 'tutorials')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TutorialLibrary $tutorialLibrary = null;

    /**
     * @var Collection<int, TutorialSet>
     */
    #[ORM\ManyToMany(targetEntity: TutorialSet::class, mappedBy: 'tutorials')]
    private Collection $tutorialSets;

    public function __construct()
    {
        $this->tutorialSets = new ArrayCollection();
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

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getTutorialLibrary(): ?TutorialLibrary
    {
        return $this->tutorialLibrary;
    }

    public function setTutorialLibrary(?TutorialLibrary $tutorialLibrary): static
    {
        $this->tutorialLibrary = $tutorialLibrary;

        return $this;
    }

    /**
     * @return Collection<int, TutorialSet>
     */
    public function getTutorialSets(): Collection
    {
        return $this->tutorialSets;
    }

    public function addTutorialSet(TutorialSet $tutorialSet): static
    {
        if (!$this->tutorialSets->contains($tutorialSet)) {
            $this->tutorialSets->add($tutorialSet);
            $tutorialSet->addTutorial($this);
        }

        return $this;
    }

    public function removeTutorialSet(TutorialSet $tutorialSet): static
    {
        if ($this->tutorialSets->removeElement($tutorialSet)) {
            $tutorialSet->removeTutorial($this);
        }

        return $this;
    }
    
    public function __toString(): string
    {
        return $this->name;
    }
}
