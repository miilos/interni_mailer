<?php

namespace App\Validator\UniqueTemplateName;

use App\Dto\EmailTemplateDto;
use App\Dto\EmailTwigTemplateDto;
use App\Repository\EmailTemplateRepository;
use App\Repository\EmailTwigTemplateRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * both EmailTemplate and EmailTwigTemplate names should be unique, so this validator works for both
 */
class UniqueTemplateNameValidator extends ConstraintValidator
{
    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
        private EmailTwigTemplateRepository  $emailTwigTemplateRepository
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

        $allNames = [];

        if ($this->context->getObject() instanceof EmailTemplateDto) {
            $allNames = $this->emailTemplateRepository->getAllEmailTemplateNames();
        }

        if ($this->context->getObject() instanceof EmailTwigTemplateDto) {
            $allNames =  $this->emailTwigTemplateRepository->getAllEmailTwigTemplateNames();
        }

        foreach ($allNames as $name) {
            if ($value === $name['name']) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
