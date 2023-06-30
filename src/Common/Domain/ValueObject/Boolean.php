<?php

declare(strict_types=1);

namespace App\Common\Domain\ValueObject;

class Boolean
{
    private ?bool $value;

    public function __construct(?bool $value)
    {
        $this->value = $value ?? null;
    }

    public function isValid(): bool
    {
        return is_bool($this->value);
    }

    public function toString(): string
    {
        if ($this->value === true) {
            return 'Yes';
        }

        if ($this->value === false) {
            return 'No';
        }

        return 'N/A';
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
