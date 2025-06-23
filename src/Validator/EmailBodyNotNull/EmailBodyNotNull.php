<?php

namespace App\Validator\EmailBodyNotNull;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class EmailBodyNotNull extends Constraint
{
    public string $message = 'Either the email body or a body template name must be specified!';

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
        return self::CLASS_CONSTRAINT;
    }
}
