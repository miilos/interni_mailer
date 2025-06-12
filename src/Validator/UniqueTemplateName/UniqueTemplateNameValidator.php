<?php

namespace App\Validator\UniqueTemplateName;

use App\Repository\EmailTemplateRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueTemplateNameValidator extends ConstraintValidator
{
    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueTemplateName) {
            throw new UnexpectedTypeException($constraint, UniqueTemplateName::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $allNames = $this->emailTemplateRepository->getAllEmailTemplateNames();
        foreach ($allNames as $name) {
            if ($value === $name['name']) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
