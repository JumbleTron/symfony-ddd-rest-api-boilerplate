<?php

declare(strict_types=1);

namespace App\Common\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class ConstraintEntityExists extends Constraint
{
    public const NOT_EXISTS_ERROR = '3024e2a5-7790-4300-a5ee-f45fe9fdf4a1';

    /**
     * @var string[]
     */
    protected static $errorNames = [
        self::NOT_EXISTS_ERROR => 'NOT_EXISTS_ERROR',
    ];

    public string $message = 'entity doesn\'t exist';

    public ?string $entityClass = null;

    public ?string $entityProperty = null;

    public function getRequiredOptions(): array
    {
        return ['entityClass'];
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
