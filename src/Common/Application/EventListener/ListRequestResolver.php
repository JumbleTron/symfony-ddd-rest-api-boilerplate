<?php

declare(strict_types=1);

namespace App\Common\Application\EventListener;

use App\Common\Application\Dto\Pagination\Paginationable;
use App\Common\Application\Dto\Search\Searchable;
use App\Common\Application\Dto\Sort\Sortable;
use App\Common\Domain\Exception\ApiException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TypeError;

class ListRequestResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if ($request->getMethod() !== 'GET') {
            return false;
        }

        if (is_a($argument->getType(), Paginationable::class, true)) {
            return true;
        }

        if (is_a($argument->getType(), Sortable::class, true)) {
            return true;
        }

        if (is_a($argument->getType(), Searchable::class, true)) {
            return true;
        }

        return false;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        try {
            $dto = $this->serializer->deserialize(
                json_encode($request->query->all()),
                $argument->getType(),
                'json',
                [
                    AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
                    DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true,
                ]
            );
        } catch (TypeError $e) {
            $this->logger->debug($e->getMessage());
            throw ApiException::forBadRequest();
        }

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $messages = [];
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()][] = $error->getMessage();
            }

            throw ApiException::forBadRequest($messages);
        }

        yield $dto;
    }
}
