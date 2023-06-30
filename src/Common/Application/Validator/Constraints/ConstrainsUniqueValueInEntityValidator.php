<?php

declare(strict_types=1);

namespace App\Common\Application\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ConstrainsUniqueValueInEntityValidator extends ConstraintValidator
{
    public function __construct(private readonly ManagerRegistry $registry, private readonly RequestStack $requestStack)
    {
    }

    //@phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ConstrainsUniqueValueInEntity) {
            throw new UnexpectedTypeException($constraint, ConstrainsUniqueValueInEntity::class);
        }

        if (!is_string($constraint->field)) {
            throw new UnexpectedTypeException($constraint->field, 'string');
        }

        if (!is_string($constraint->entityClass)) {
            throw new UnexpectedTypeException($constraint->entityClass, 'string');
        }

        if ($value === null) {
            return;
        }

        $em = $this->registry->getManagerForClass($constraint->entityClass);

        if (!$em instanceof EntityManagerInterface) {
            throw new ConstraintDefinitionException(
                sprintf(
                    'Unable to find the object manager associated with an entity of class "%s".',
                    $constraint->entityClass
                )
            );
        }

        $class = $em->getClassMetadata($constraint->entityClass);
        if (!$class->hasField($constraint->field) && !$class->hasAssociation($constraint->field)) {
            throw new ConstraintDefinitionException(
                sprintf(
                    'The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.',
                    $constraint->field
                )
            );
        }
        $repository = $em->getRepository($constraint->entityClass);

        $searchResults = $repository->findOneBy([
            $constraint->field => $value,
        ]);
        if ($searchResults === null) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        $routeParams = $request->attributes->get('_route_params');
        if (
            isset($routeParams[$constraint->routeParamIdName])
            && in_array($request->getRealMethod(), [Request::METHOD_PUT, Request::METHOD_PATCH])
            && (string)$searchResults->getId() === $routeParams[$constraint->routeParamIdName]
        ) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setCode(ConstrainsUniqueValueInEntity::NOT_UNIQUE_ERROR)
            ->addViolation();
    }
}
