<?php

namespace App\Entity;

use App\Repository\FrontOfficeTemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FrontOfficeTemplateRepository::class)]
class FrontOfficeTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $css;

    #[ORM\Column(type: 'text', nullable: true)]
    private $stylesheets;

    #[ORM\Column(type: 'text')]
    private $html;

    #[ORM\Column(type: 'text', nullable: true)]
    private $js;

    #[ORM\Column(type: 'text', nullable: true)]
    private $scripts;

    #[ORM\OneToMany(mappedBy: 'template', targetEntity: FrontOfficePage::class, orphanRemoval: true)]
    private $frontOfficePages;

    public function __construct()
    {
        $this->frontOfficePages = new ArrayCollection();
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

    public function getCss(): ?string
    {
        return $this->css;
    }

    public function setCss(?string $css): self
    {
        $this->css = $css;

        return $this;
    }

    public function getStylesheets(): ?string
    {
        return $this->stylesheets;
    }

    public function setStylesheets(?string $stylesheets): self
    {
        $this->stylesheets = $stylesheets;

        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function getJs(): ?string
    {
        return $this->js;
    }

    public function setJs(?string $js): self
    {
        $this->js = $js;

        return $this;
    }

    public function getScripts(): ?string
    {
        return $this->scripts;
    }

    public function setScripts(?string $scripts): self
    {
        $this->scripts = $scripts;

        return $this;
    }

    /**
     * @return Collection<int, FrontOfficePage>
     */
    public function getFrontOfficePages(): Collection
    {
        return $this->frontOfficePages;
    }

    public function addFrontOfficePage(FrontOfficePage $frontOfficePage): self
    {
        if (!$this->frontOfficePages->contains($frontOfficePage)) {
            $this->frontOfficePages[] = $frontOfficePage;
            $frontOfficePage->setTemplate($this);
        }

        return $this;
    }

    public function removeFrontOfficePage(FrontOfficePage $frontOfficePage): self
    {
        if ($this->frontOfficePages->removeElement($frontOfficePage)) {
            // set the owning side to null (unless already changed)
            if ($frontOfficePage->getTemplate() === $this) {
                $frontOfficePage->setTemplate(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->name;
    }
}
