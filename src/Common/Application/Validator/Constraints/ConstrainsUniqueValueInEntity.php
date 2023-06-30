<?php

declare(strict_types=1);

namespace App\Common\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class ConstrainsUniqueValueInEntity extends Constraint
{
    public const NOT_UNIQUE_ERROR = '032f75b3c-a02a393196a8-18328bd32e8';

    /**
     * @var string[]
     */
    protected static $errorNames = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    public string $message = 'This value is already used.';

    public ?string $field = null;

    public ?string $entityClass = null;

    public ?string $routeParamIdName = 'id';


    public function getRequiredOptions(): array
    {
        return ['entityClass', 'field'];
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
