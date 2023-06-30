<?php

declare(strict_types=1);

namespace App\Common\Application\Service;

use Nelmio\ApiDocBundle\PropertyDescriber\PropertyDescriberInterface;
use OpenApi\Annotations\Schema;
use Symfony\Component\Uid\Uuid;

class UuidInterfacePropertyDescriber implements PropertyDescriberInterface
{
    // phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function describe(array $types, Schema $property, array $groups = null)
    {
        $property->type = 'string';
        $property->format = 'uuid';
    }

    public function supports(array $types): bool
    {
        return count($types) === 1 && is_a($types[0]->getClassName(), Uuid::class, true);
    }
}
