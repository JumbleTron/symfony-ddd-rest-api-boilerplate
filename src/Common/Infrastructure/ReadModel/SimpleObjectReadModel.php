<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\ReadModel;

use Symfony\Component\Uid\Uuid;

class SimpleObjectReadModel
{
    public function __construct(public readonly Uuid $id, public readonly string $name)
    {
    }
}
