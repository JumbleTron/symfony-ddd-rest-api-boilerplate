<?php

declare(strict_types=1);

namespace App\Common\Domain\ValueObject;

class BooleanString
{
    private ?string $value;

    public function __construct(?string $value)
    {
        $this->value = $value ? strtolower($value) : null;
    }

    public function isValid(): bool
    {
        if ($this->value === null) {
            return false;
        }

        return $this->value === 'true' ||
            $this->value === '1' ||
            $this->value === 'yes' ||
            $this->value === 'false' ||
            $this->value === '0' ||
            $this->value === 'no';
    }

    public function getValue(): ?bool
    {
        if ($this->value === 'true' || $this->value === '1' || $this->value === 'yes') {
            return true;
        }

        if ($this->value === 'false' || $this->value === '0' || $this->value === 'no') {
            return false;
        }

        return null;
    }
}
