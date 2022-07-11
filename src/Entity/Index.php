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
    #[ORM\JoinColumn(nullable: false)]
    private $model;

    #[ORM\OneToMany(targetEntity: IndexColumn::class, mappedBy: 'view', orphanRemoval: true)]
    private $indexColumns;

    public function __construct()
    {
        $this->columns = new ArrayCollection();
        $this->indexColumns = new ArrayCollection();
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
}
