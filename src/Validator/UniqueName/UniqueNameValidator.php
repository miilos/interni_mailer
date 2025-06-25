<?php

namespace App\Validator\UniqueName;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueNameValidator extends ConstraintValidator
{
    public function __construct(
        private ManagerRegistry $managerRegistry
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueName) {
            throw new UnexpectedTypeException($constraint, UniqueName::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $repo = $this->managerRegistry->getRepository($constraint->entityClass);

        if (!method_exists($repo, $constraint->repoMethod)) {
            throw new \RuntimeException(sprintf('Method "%s" does not exist in "%s"', $constraint->repoMethod, $repo::class));
        }

        $allValues = $repo->{$constraint->repoMethod}();

        foreach ($allValues as $val) {
            if ($value === $val[$constraint->uniqueProperty]) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
