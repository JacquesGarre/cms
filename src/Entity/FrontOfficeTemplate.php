<?php

namespace App\Entity;

use App\Repository\FrontOfficeTemplateRepository;
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
}
