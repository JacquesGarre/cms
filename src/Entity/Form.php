<?php

namespace App\Entity;

use App\Repository\FormRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormRepository::class)]
class Form
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'form', targetEntity: Attribute::class, orphanRemoval: true)]
    #[ORM\OrderBy(["position" => "ASC"])]
    private $attributes;

    #[ORM\OneToMany(mappedBy: 'model', targetEntity: Index::class, orphanRemoval: true)]
    private $indices;

    #[ORM\OneToMany(mappedBy: 'model', targetEntity: Entity::class, orphanRemoval: true)]
    private $entities;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->indices = new ArrayCollection();
        $this->entities = new ArrayCollection();
    }

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

    public function __toString() {
        return $this->name;
    }

    /**
     * @return Collection<int, Attribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function addAttribute(Attribute $attribute): self
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes[] = $attribute;
            $attribute->setForm($this);
        }

        return $this;
    }

    public function removeAttribute(Attribute $attribute): self
    {
        if ($this->attributes->removeElement($attribute)) {
            // set the owning side to null (unless already changed)
            if ($attribute->getForm() === $this) {
                $attribute->setForm(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Index>
     */
    public function getIndices(): Collection
    {
        return $this->indices;
    }

    public function addIndex(Index $index): self
    {
        if (!$this->indices->contains($index)) {
            $this->indices[] = $index;
            $index->setModel($this);
        }

        return $this;
    }

    public function removeIndex(Index $index): self
    {
        if ($this->indices->removeElement($index)) {
            // set the owning side to null (unless already changed)
            if ($index->getModel() === $this) {
                $index->setModel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Entity>
     */
    public function getEntities(): Collection
    {
        return $this->entities;
    }

    public function addEntity(Entity $entity): self
    {
        if (!$this->entities->contains($entity)) {
            $this->entities[] = $entity;
            $entity->setModel($this);
        }

        return $this;
    }

    public function removeEntity(Entity $entity): self
    {
        if ($this->entities->removeElement($entity)) {
            // set the owning side to null (unless already changed)
            if ($entity->getModel() === $this) {
                $entity->setModel(null);
            }
        }

        return $this;
    }

}
