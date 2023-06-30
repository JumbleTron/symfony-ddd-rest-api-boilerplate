<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\WriteModel;

use Symfony\Component\Serializer\Annotation\Ignore;

abstract class AbstractWriteModel implements WriteModel
{
    protected array $passedField = [];

    #[Ignore]
    public function getValidationGroup(): array
    {
        return [];
    }

    #[Ignore]
    public function getPassedField(): array
    {
        return $this->passedField;
    }

    #[Ignore]
    public function isFieldDirty(string $name): bool
    {
        return in_array(trim($name), $this->passedField);
    }

    #[Ignore]
    public function setPassedField(array $passedField): self
    {
        $this->passedField = $passedField;

        return $this;
    }
}
