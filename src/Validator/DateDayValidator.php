<?php

declare(strict_types=1);

namespace JNV\DueDateCalculator\Validator;

use JNV\DueDateCalculator\Exception\InvalidDateDayException;
use JNV\DueDateCalculator\Util\WorkintTimeConfig;

class DateDayValidator implements DateValidatorInterface
{
    public function validate(\DateTimeInterface $dateTime): void
    {
        if (WorkintTimeConfig::FRIDAY_NUMERIC_REPRESENTATION < $dateTime->format('N')) {
            throw new InvalidDateDayException(
                sprintf(
                    'Provided date should be a weekday, %s provided!',
                    $dateTime->format('l'),
                ),
            );
        }
    }
}
