<?php

declare(strict_types=1);

namespace App\Common\Application\Validator;

use App\Common\Domain\Exception\ApiException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WriteModelValidator implements WriteModelValidatorInterface
{
    public function __construct(
        protected readonly ValidatorInterface $validator
    ) {
    }


    public function validate(Request $request, object $model): void
    {
        $validationGroup = [strtolower($request->getRealMethod())];
        if (!empty($model->getValidationGroup())) {
            $validationGroup = array_merge($model->getValidationGroup(), $validationGroup);
        }
        $errors = $this->validator->validate($model, null, $validationGroup);

        if (count($errors) > 0) {
            $messages = [];
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $messages[$this->fieldNameToSnakeCase($error->getPropertyPath())][] = $error->getMessage();
            }

            throw ApiException::forUnprocessableEntity($messages);
        }
    }

    private function fieldNameToSnakeCase(string $fieldName): string
    {
        return ltrim(
            strtolower(
                preg_replace(
                    '/[A-Z]([A-Z](?![a-z]))*/',
                    '_$0',
                    $fieldName
                )
            ),
            '_'
        );
    }
}
