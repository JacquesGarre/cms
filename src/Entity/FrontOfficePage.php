<?php

namespace App\Entity;

use App\Repository\FrontOfficePageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FrontOfficePageRepository::class)]
class FrontOfficePage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $uri;

    #[ORM\ManyToOne(targetEntity: FrontOfficeTemplate::class, inversedBy: 'frontOfficePages')]
    #[ORM\JoinColumn(nullable: false)]
    private $template;

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

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getTemplate(): ?FrontOfficeTemplate
    {
        return $this->template;
    }

    public function setTemplate(?FrontOfficeTemplate $template): self
    {
        $this->template = $template;

        return $this;
    }
}
