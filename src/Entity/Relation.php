<?php

namespace App\Entity;

use App\Repository\RelationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RelationRepository::class)]
class Relation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'relations')]
    private $model;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $position;

    #[ORM\ManyToOne(targetEntity: Index::class, inversedBy: 'relations')]
    #[ORM\JoinColumn(nullable: false)]
    private $view;

    #[ORM\ManyToOne(targetEntity: Attribute::class, inversedBy: 'relations')]
    #[ORM\JoinColumn(nullable: false)]
    private $mappedBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?Form
    {
        return $this->model;
    }

    public function setModel(?Form $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
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

    public function getMappedBy(): ?Attribute
    {
        return $this->mappedBy;
    }

    public function setMappedBy(?Attribute $mappedBy): self
    {
        $this->mappedBy = $mappedBy;

        return $this;
    }

}
