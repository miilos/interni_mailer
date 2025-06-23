<?php

namespace App\Validator\EmailBodyNotNull;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class EmailBodyNotNullValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof EmailBodyNotNull) {
            throw new UnexpectedTypeException($constraint, EmailBodyNotNull::class);
        }

        if (!is_object($value)) {
            throw new UnexpectedValueException($value, 'object');
        }

        if (!$value->getBody() && !$value->getBodyTemplate()) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }

}
