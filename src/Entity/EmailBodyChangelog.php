<?php

namespace App\Entity;

use App\Repository\EmailBodyChangelogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: EmailBodyChangelogRepository::class)]
class EmailBodyChangelog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fullTemplateData'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fullTemplateData'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['fullTemplateData'])]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fullTemplateData'])]
    private ?string $extension = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['fullTemplateData'])]
    private ?string $parsedBodyHtml = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['fullTemplateData'])]
    private ?array $variables = null;

    #[ORM\ManyToOne(inversedBy: 'changelog')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EmailBody $template = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['fullTemplateData'])]
    private ?array $diff = null;

    #[ORM\Column]
    #[Groups(['fullTemplateData'])]
    private ?\DateTimeImmutable $createdAt = null;

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

    public function setParsedBodyHtml(string $parsedBodyHtml): static
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

    public function getTemplate(): ?EmailBody
    {
        return $this->template;
    }

    public function setTemplate(?EmailBody $template): static
    {
        $this->template = $template;

        return $this;
    }

    public function getDiff(): ?array
    {
        return $this->diff;
    }

    public function setDiff(?array $diff): static
    {
        $this->diff = $diff;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
