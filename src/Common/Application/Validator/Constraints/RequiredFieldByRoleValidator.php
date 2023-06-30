<?php

declare(strict_types=1);

namespace App\Common\Application\Validator\Constraints;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlankValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RequiredFieldByRoleValidator extends NotBlankValidator
{
    public function __construct(private readonly Security $security)
    {
    }

    //@phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof RequiredFieldByRole) {
            throw new UnexpectedTypeException($constraint, RequiredFieldByRole::class);
        }

        if (!in_array($constraint->role, $this->security->getUser()->getRoles())) {
            return;
        }

        parent::validate($value, $constraint);
    }
}
