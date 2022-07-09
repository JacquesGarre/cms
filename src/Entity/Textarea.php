<?php

namespace App\Entity;

use App\Repository\TextareaRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Field;

#[ORM\Entity(repositoryClass: TextareaRepository::class)]
class Textarea extends Field
{

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $placeholder;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $readonly;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $cols;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $rowscount;

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function setPlaceholder(?string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function isReadonly(): ?bool
    {
        return $this->readonly;
    }

    public function setReadonly(?bool $readonly): self
    {
        $this->readonly = $readonly;

        return $this;
    }

    public function getCols(): ?int
    {
        return $this->cols;
    }

    public function setCols(?int $cols): self
    {
        $this->cols = $cols;

        return $this;
    }

    public function getRowscount(): ?int
    {
        return $this->rowscount;
    }

    public function setRowscount(?int $rowscount): self
    {
        $this->rowscount = $rowscount;

        return $this;
    }

}
