<?php

namespace App\Entity;

use App\Repository\TutorialRepository;
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

    #[ORM\ManyToOne(inversedBy: 'tutorials')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ListTutorials $listTutorials = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

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

    public function getListTutorials(): ?ListTutorials
    {
        return $this->listTutorials;
    }

    public function setListTutorials(?ListTutorials $listTutorials): static
    {
        $this->listTutorials = $listTutorials;

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
}
