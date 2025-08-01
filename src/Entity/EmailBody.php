<?php

namespace App\Entity;

use App\Repository\EmailBodyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

#[ORM\Entity(repositoryClass: EmailBodyRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class EmailBody
{
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $extension = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $parsedBodyHtml = null;

    #[ORM\Column(nullable: true)]
    private ?array $variables = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): static
    {
        $this->extension = $extension;

        return $this;
    }

    public function getParsedBodyHtml(): ?string
    {
        return $this->parsedBodyHtml;
    }

    public function setParsedBodyHtml(?string $parsedBodyHtml): static
    {
        $this->parsedBodyHtml = $parsedBodyHtml;

        return $this;
    }

    public function getVariables(): ?array
    {
        return $this->variables;
    }

    public function setVariables(?array $variables): static
    {
        $this->variables = $variables;

        return $this;
    }
}
