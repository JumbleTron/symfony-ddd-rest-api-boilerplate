<?php

declare(strict_types=1);

namespace App\Common\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

// phpcs:disable SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
// phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
abstract class AbstractEnumType extends Type
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }
        return null;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (enum_exists($this->supports(), true) === false) {
            throw new \InvalidArgumentException("This class should be an enum");
        }

        return $this::supports()::tryFrom($value);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'TEXT';
    }

    abstract public static function supports(): string;
}
