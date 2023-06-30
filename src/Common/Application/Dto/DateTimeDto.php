<?php

declare(strict_types=1);

namespace App\Common\Application\Dto;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class DateTimeDto
{
    public static function createDateWithoutTimeFromAtom(string $dateString): DateTime
    {
        return DateTime::createFromFormat(DateTimeInterface::ATOM, $dateString)->setTime(0, 0);
    }

    public static function createDateFromAtom(string $dateString): DateTime
    {
        return DateTime::createFromFormat(DateTimeInterface::ATOM, $dateString)
            ->setTimezone(new DateTimeZone('UTC'));
    }

    public static function createFromUkTime(string $dateString): DateTime
    {
        return DateTime::createFromFormat('h:i A', $dateString);
    }

    public static function createImmutableDateFromAtom(string $dateString): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $dateString)
            ->setTimezone(new DateTimeZone('UTC'));
    }
}
