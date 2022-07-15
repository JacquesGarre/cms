<?php

namespace App\Entity;

use App\Repository\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntityRepository::class)]
class Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $creationDate;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updateDate;

    #[ORM\OneToMany(mappedBy: 'entity', targetEntity: EntityMeta::class, orphanRemoval: true)]
    private $entityMetas;

    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'entities')]
    #[ORM\JoinColumn(nullable: false)]
    private $model;

    public function __construct()
    {
        $this->entityMetas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(?\DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * @return Collection<int, EntityMeta>
     */
    public function getEntityMetas(): Collection
    {
        return $this->entityMetas;
    }

    public function getEntityMeta(string $name): mixed
    {
        return $this->getEntityMetas()->filter(function(EntityMeta $entityMeta) use ($name) {
            return $entityMeta->getName() == $name;
        })->first();
    }

    public function get(string $name): mixed
    {
        return $this->getEntityMetas()->filter(function(EntityMeta $entityMeta) use ($name) {
            return $entityMeta->getName() == $name;
        })->first()->getValue();
    }

    public function addEntityMeta(EntityMeta $entityMeta): self
    {
        if (!$this->entityMetas->contains($entityMeta)) {
            $this->entityMetas[] = $entityMeta;
            $entityMeta->setEntity($this);
        }

        return $this;
    }

    public function removeEntityMeta(EntityMeta $entityMeta): self
    {
        if ($this->entityMetas->removeElement($entityMeta)) {
            // set the owning side to null (unless already changed)
            if ($entityMeta->getEntity() === $this) {
                $entityMeta->setEntity(null);
            }
        }

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
}
