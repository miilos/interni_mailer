<?php

namespace App\Dto;

use App\Validator\SupportedBodyFileExtension\SupportedBodyFileExtension;
use App\Validator\UniqueTemplateName\UniqueTemplateName;
use Symfony\Component\Validator\Constraints as Assert;

class EmailBodyDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'The template name can\'t be blank!')]
        #[UniqueTemplateName]
        private ?string $name = null,

        #[Assert\NotBlank(message: 'The content of the body can\'t be blank!')]
        private ?string $content = null,

        #[SupportedBodyFileExtension]
        private ?string $extension = null
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
}
