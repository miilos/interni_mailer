<?php

namespace App\Validator\SupportedBodyFileExtension;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class SupportedBodyFileExtensionValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof SupportedBodyFileExtension) {
            throw new UnexpectedTypeException($constraint, SupportedBodyFileExtension::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!SupportedBodyFileExtensionsEnum::tryFrom($value)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
