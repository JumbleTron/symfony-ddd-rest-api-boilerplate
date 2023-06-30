<?php

declare(strict_types=1);

namespace App\Common\Application\Validator\Constraints;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RoleValidator extends ConstraintValidator
{
    public function __construct(private readonly AuthorizationCheckerInterface $checker)
    {
    }

    //@phpcs:ignore
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Role) {
            throw new UnexpectedTypeException($constraint, Role::class);
        }

        $roleAllowed = $constraint->role;

        if ($this->checker->isGranted($roleAllowed)) {
            return;
        }

        if ($value === null) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
