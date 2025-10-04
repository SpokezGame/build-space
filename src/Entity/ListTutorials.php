<?php

namespace App\Entity;

use App\Repository\ListTutorialsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListTutorialsRepository::class)]
class ListTutorials
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Tutorial>
     */
    #[ORM\OneToMany(targetEntity: Tutorial::class, mappedBy: 'listTutorials', orphanRemoval: true, cascade: ['persist'])]
    private Collection $tutorials;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    public function __construct()
    {
        $this->tutorials = new ArrayCollection();
    }
    
    public function __toString(){
        return "author : " . $this->author; 
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

    public function addTutorial(Tutorial $tutorial)
    {
        if (!$this->tutorials->contains($tutorial)) {
            $this->tutorials->add($tutorial);
            $tutorial->setListTutorials($this);
        }
        return $this;
    }

    public function removeTutorial(Tutorial $tutorial)
    {
        if ($this->tutorials->removeElement($tutorial)) {
            // set the owning side to null (unless already changed)
            if ($tutorial->getListTutorials() === $this) {
                $tutorial->setListTutorials(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author)
    {
        $this->author = $author;

        return $this;
    }
}
