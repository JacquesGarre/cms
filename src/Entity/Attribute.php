<?php

namespace App\Entity;

use App\Repository\AttributeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttributeRepository::class)]
class Attribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: Label::class, cascade: ['persist', 'remove'])]
    private $label;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $disabled;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $required;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $col;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $position;

    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'attributes')]
    #[ORM\JoinColumn(nullable: false)]
    private $form;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $checked;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $placeholder;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $readonly;

    #[ORM\Column(type: 'string', length: 255)]
    private $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $defaultValue;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $multiple;

    #[ORM\ManyToMany(targetEntity: Option::class)]
    private $options;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $cols;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $height;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?Label
    {
        return $this->label;
    }

    public function setLabel(?Label $label): self
    {
        $this->label = $label;

        return $this;
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

    public function isDisabled(): ?bool
    {
        return $this->disabled;
    }

    public function setDisabled(?bool $disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function isRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(?bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function getCol(): ?int
    {
        return $this->col;
    }

    public function setCol(?int $col): self
    {
        $this->col = $col;

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

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setForm(?Form $form): self
    {
        $this->form = $form;

        return $this;
    }

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(?string $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function isMultiple(): ?bool
    {
        return $this->multiple;
    }

    public function setMultiple(?bool $multiple): self
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
        }

        return $this;
    }

    public function removeOption(Option $option): self
    {
        $this->options->removeElement($option);

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

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function __toString() {
        return $this->label;
    }

}
