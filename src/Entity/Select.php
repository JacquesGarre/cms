<?php

namespace App\Entity;

use App\Repository\SelectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Field;

#[ORM\Entity(repositoryClass: SelectRepository::class)]
#[ORM\Table(name: '`select`')]
class Select extends Field
{
    #[ORM\Column(type: 'boolean', nullable: true)]
    private $multiple;

    #[ORM\ManyToMany(targetEntity: Option::class, inversedBy: 'selects')]
    private $options;

    public function __construct()
    {
        $this->options = new ArrayCollection();
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

}
