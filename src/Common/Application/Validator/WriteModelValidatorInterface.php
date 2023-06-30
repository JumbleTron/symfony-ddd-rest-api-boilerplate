<?php

declare(strict_types=1);

namespace App\Common\Application\Validator;

use Symfony\Component\HttpFoundation\Request;

interface WriteModelValidatorInterface
{
    public function validate(Request $request, object $model): void;
}
