<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class EmailVariableDto
{
    public function __construct(
        #[Assert\NotNull(message: 'The name of the variable can\'t be blank!')]
        private string $name,

        #[Assert\NotNull(message: 'A variable must have a value!')]
        private string $value
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
