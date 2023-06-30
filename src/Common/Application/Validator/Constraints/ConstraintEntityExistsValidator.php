<?php

declare(strict_types=1);

namespace App\Common\Application\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ConstraintEntityExistsValidator extends ConstraintValidator
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ConstraintEntityExists) {
            throw new UnexpectedTypeException($constraint, ConstraintEntityExists::class);
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


        $field = $constraint->entityProperty ?? 'id';
        if ($field === 'id' && !Uuid::isValid($value)) {
            return;
        }

        $searchResults = $em->getRepository($constraint->entityClass)->findOneBy([$field => $value]);

        if ($searchResults !== null) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setCode(ConstraintEntityExists::NOT_EXISTS_ERROR)
            ->addViolation();
    }
}
