<?php

namespace App\Entity;

use App\Repository\EmailBodyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: EmailBodyRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class EmailBody
{
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fullTemplateData', 'basicTemplateData'])]
    #[Map(if: false)]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['fullTemplateData', 'basicTemplateData'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['fullTemplateData', 'basicTemplateData'])]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fullTemplateData', 'basicTemplateData'])]
    private ?string $extension = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['fullTemplateData', 'basicTemplateData'])]
    private ?string $parsedBodyHtml = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['fullTemplateData', 'basicTemplateData'])]
    private ?array $variables = null;

    /**
     * @var Collection<int, EmailBodyChangelog>
     */
    #[ORM\OneToMany(targetEntity: EmailBodyChangelog::class, mappedBy: 'template')]
    #[Groups(['fullTemplateData'])]
    private Collection $changelog;

    public function __construct()
    {
        $this->changelog = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, EmailBodyChangelog>
     */
    public function getChangelog(): Collection
    {
        return $this->changelog;
    }

    public function addChangelog(EmailBodyChangelog $changelog): static
    {
        if (!$this->changelog->contains($changelog)) {
            $this->changelog->add($changelog);
            $changelog->setTemplate($this);
        }

        return $this;
    }

    public function removeChangelog(EmailBodyChangelog $changelog): static
    {
        if ($this->changelog->removeElement($changelog)) {
            // set the owning side to null (unless already changed)
            if ($changelog->getTemplate() === $this) {
                $changelog->setTemplate(null);
            }
        }

        return $this;
    }
}
