<?php

namespace App\Dto;

use App\Validator\UniqueName\UniqueName;
use Symfony\Component\Validator\Constraints as Assert;

class EmailTwigTemplateDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Twig template name can\'t be blank!')]
        #[UniqueName]
        private ?string $name = null,

        #[Assert\NotBlank(message: 'Twig template file path can\'t be blank!')]
        private ?string $filePath = null
    ) {}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }
}
