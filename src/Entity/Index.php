<?php

namespace App\Entity;

use App\Repository\IndexRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndexRepository::class)]
#[ORM\Table(name: '`index`')]
class Index
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'indices')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private $model;

    #[ORM\OneToMany(targetEntity: IndexColumn::class, mappedBy: 'view', orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private $indexColumns;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $pagination;

    #[ORM\ManyToOne(targetEntity: IndexColumn::class)]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private $orderBy;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $orderDirection;

    #[ORM\OneToMany(mappedBy: 'view', targetEntity: Relation::class, orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private $relations;

    public function __construct()
    {
        $this->columns = new ArrayCollection();
        $this->indexColumns = new ArrayCollection();
        $this->relations = new ArrayCollection();
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

    public function getModel(): ?Form
    {
        return $this->model;
    }

    public function setModel(?Form $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return Collection<int, IndexColumn>
     */
    public function getIndexColumns(): Collection
    {
        return $this->indexColumns;
    }

    public function addIndexColumn(IndexColumn $indexColumn): self
    {
        if (!$this->indexColumns->contains($indexColumn)) {
            $this->indexColumns[] = $indexColumn;
            $indexColumn->setView($this);
        }

        return $this;
    }

    public function __toString() {
        return $this->name;
    }

    public function removeIndexColumn(IndexColumn $indexColumn): self
    {
        if ($this->indexColumns->removeElement($indexColumn)) {
            // set the owning side to null (unless already changed)
            if ($indexColumn->getView() === $this) {
                $indexColumn->setView(null);
            }
        }

        return $this;
    }

    public function getPagination(): ?int
    {
        return $this->pagination;
    }

    public function setPagination(?int $pagination): self
    {
        $this->pagination = $pagination;

        return $this;
    }

    public function getOrderBy(): ?IndexColumn
    {
        return $this->orderBy;
    }

    public function setOrderBy(?IndexColumn $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function getOrderDirection(): ?string
    {
        return $this->orderDirection;
    }

    public function setOrderDirection(?string $orderDirection): self
    {
        $this->orderDirection = $orderDirection;

        return $this;
    }

    /**
     * @return Collection<int, Relation>
     */
    public function getRelations(): Collection
    {
        return $this->relations;
    }

    public function addRelation(Relation $relation): self
    {
        if (!$this->relations->contains($relation)) {
            $this->relations[] = $relation;
            $relation->setView($this);
        }

        return $this;
    }

    public function removeRelation(Relation $relation): self
    {
        if ($this->relations->removeElement($relation)) {
            // set the owning side to null (unless already changed)
            if ($relation->getView() === $this) {
                $relation->setView(null);
            }
        }

        return $this;
    }
}
