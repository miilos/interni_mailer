<?php

namespace App\Validator\UniqueName;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueName extends Constraint
{
    public string $entityClass;
    public string $repoMethod;
    public string $uniqueProperty;
    public string $message = 'This template name is already in use!';

    public function __construct(
        string $entityClass,
        string $repoMethod,
        string $uniqueProperty = 'name',
        ?string $message = null,
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null
    )
    {
        parent::__construct($options, $groups, $payload);
        $this->entityClass = $entityClass;
        $this->repoMethod = $repoMethod;
        $this->uniqueProperty = $uniqueProperty;
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
