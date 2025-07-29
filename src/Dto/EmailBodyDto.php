<?php

namespace App\Dto;

use App\Entity\EmailBody;
use App\Validator\SupportedBodyFileExtension\SupportedBodyFileExtension;
use App\Validator\UniqueName\UniqueName;
use Symfony\Component\Validator\Constraints as Assert;

class EmailBodyDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'The template name can\'t be blank!')]
        #[UniqueName(
            entityClass: EmailBody::class,
            repoMethod: 'getAllBodyTemplateNames'
        )]
        private ?string $name = null,

        #[Assert\NotBlank(message: 'The content of the body can\'t be blank!')]
        private ?string $content = null,

        #[SupportedBodyFileExtension]
        private ?string $extension = null,

        private array $variables = [],
    ) {}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }
}
