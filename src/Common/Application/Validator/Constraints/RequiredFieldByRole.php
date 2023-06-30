<?php

declare(strict_types=1);

namespace App\Common\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @Annotation
 */
class RequiredFieldByRole extends NotBlank
{
    public string $role;
}
