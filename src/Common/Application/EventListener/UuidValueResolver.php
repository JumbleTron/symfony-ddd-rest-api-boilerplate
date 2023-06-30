<?php

declare(strict_types=1);

namespace App\Common\Application\EventListener;

use App\Common\Domain\Exception\ApiException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

class UuidValueResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return !$argument->isVariadic()
            && is_string($request->get($argument->getName()))
            && is_a((string) $argument->getType(), Uuid::class, true);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($request, $argument)) {
            throw new RuntimeException('Unsupported method call');
        }

        $value = $request->get($argument->getName());

        if (!Uuid::isValid($value)) {
            throw ApiException::forBadRequest([sprintf('Value %s is not valid uuid string', $value)]);
        }

        yield Uuid::fromString($value);
    }
}
