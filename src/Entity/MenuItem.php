<?php

namespace App\Entity;

use App\Repository\MenuItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuItemRepository::class)]
class MenuItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $link;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $position;

    #[ORM\ManyToOne(targetEntity: Form::class)]
    private $model;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $route;

    #[ORM\ManyToOne(targetEntity: Index::class)]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private $view;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $materialIcon;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

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

    public function getModel(): ?Form
    {
        return $this->model;
    }

    public function setModel(?Form $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(?string $route): self
    {
        $this->route = $route;

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

    public function getMaterialIcon(): ?string
    {
        return $this->materialIcon;
    }

    public function setMaterialIcon(?string $materialIcon): self
    {
        $this->materialIcon = $materialIcon;

        return $this;
    }
}
