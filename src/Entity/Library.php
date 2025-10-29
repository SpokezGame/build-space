<?php

namespace App\Entity;

use App\Repository\LibraryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class that contains all the tutorials of one member
 */
#[ORM\Entity(repositoryClass: LibraryRepository::class)]
class Library
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Tutorial>
     */
    #[ORM\OneToMany(targetEntity: Tutorial::class, mappedBy: 'library', orphanRemoval: true, cascade: ['persist'])]
    private Collection $tutorials;

    #[ORM\OneToOne(mappedBy: 'library', cascade: ['persist', 'remove'])]
    private ?Member $member = null;

    public function __construct()
    {
        $this->tutorials = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Tutorial>
     */
    public function getTutorials(): Collection
    {
        return $this->tutorials;
    }

    public function addTutorial(Tutorial $tutorial): static
    {
        if (!$this->tutorials->contains($tutorial)) {
            $this->tutorials->add($tutorial);
            $tutorial->setLibrary($this);
        }

        return $this;
    }

    public function removeTutorial(Tutorial $tutorial): static
    {
        if ($this->tutorials->removeElement($tutorial)) {
            // set the owning side to null (unless already changed)
            if ($tutorial->getLibrary() === $this) {
                $tutorial->setLibrary(null);
            }
        }

        return $this;
    }
    
    public function __toString(): string
    {
        return $this->member;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(Member $member): static
    {
        // set the owning side of the relation if necessary
        if ($member->getLibrary() !== $this) {
            $member->setLibrary($this);
        }

        $this->member = $member;

        return $this;
    }
}
