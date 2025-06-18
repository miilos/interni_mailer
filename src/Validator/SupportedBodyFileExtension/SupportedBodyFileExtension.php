<?php

namespace App\Validator\SupportedBodyFileExtension;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SupportedBodyFileExtension extends Constraint
{
    public string $message = 'This is not a supported file extension for the body template file!';

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
