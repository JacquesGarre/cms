<?php

namespace App\Entity;

use App\Repository\InputRepository;
use App\Entity\Field;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InputRepository::class)]
class Input extends Field
{

    #[ORM\Column(type: 'boolean')]
    private $checked;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $placeholder;

    #[ORM\Column(type: 'boolean')]
    private $readonly;

    #[ORM\Column(type: 'string', length: 255)]
    private $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $value;

    public function isChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(?bool $checked): self
    {
        $this->checked = $checked;

        return $this;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function isReadonly(): ?bool
    {
        return $this->readonly;
    }

    public function setReadonly(bool $readonly): self
    {
        $this->readonly = $readonly;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

}
