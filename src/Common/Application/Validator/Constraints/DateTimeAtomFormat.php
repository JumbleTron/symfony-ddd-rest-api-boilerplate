<?php

declare(strict_types=1);

namespace App\Common\Application\Validator\Constraints;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints\DateTime as ConstraintDateTime;

/**
 * @Annotation
 */
class DateTimeAtomFormat extends ConstraintDateTime
{
    /**
     * @var string
     */
    public $format = DateTimeInterface::ATOM;

    public function getDefaultOption(): ?string
    {
        return 'format';
    }
}
