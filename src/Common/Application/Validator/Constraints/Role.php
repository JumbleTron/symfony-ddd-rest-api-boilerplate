<?php

declare(strict_types=1);

namespace App\Common\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Role extends Constraint
{
    public string $message = 'You are not allowed to change this field!';

    public string $role;
}
