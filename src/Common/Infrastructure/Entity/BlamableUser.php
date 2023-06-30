<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Entity;

use Symfony\Component\Uid\Uuid;

interface BlamableUser
{
    public function getId(): Uuid;
    public function getFirstName(): string;
    public function getLastName(): string;
}
