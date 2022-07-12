<?php

namespace App\Entity;

use App\Repository\IndexColumnRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndexColumnRepository::class)]
class IndexColumn
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Index::class, inversedBy: 'indexColumns')]
    private $view;

    #[ORM\ManyToOne(targetEntity: Attribute::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $field;

    #[ORM\Column(type: 'integer')]
    private $position;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getView(): ?Index
    {
        return $this->view;
    }

    public function setView(?Index $view): self
    {
        $this->view = $view;

        return $this;
    }

    public function getField(): ?Attribute
    {
        return $this->field;
    }

    public function setField(Attribute $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString() {
        return !empty($this->name) ? $this->name : $this->field->getLabel();
    }
}
