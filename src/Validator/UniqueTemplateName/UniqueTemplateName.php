<?php

namespace App\Validator\UniqueTemplateName;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueTemplateName extends Constraint
{
    public string $message = 'This template name is already in use!';

    public function __construct(?string $message = null, mixed $options = null, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct($options, $groups, $payload);
        $this->message = $message ?? $this->message;
    }

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }

    public function getTargets(): string|array
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
