<?php

declare(strict_types=1);

namespace JNV\DueDateCalculator\Validator;

use JNV\DueDateCalculator\Exception\InvalidDateTimeException;
use JNV\DueDateCalculator\Util\WorkintTimeConfig;

class DateTimeValidator implements DateValidatorInterface
{
    public function validate(\DateTimeInterface $dateTime): void
    {
        if (!$this->isHourValid($dateTime) || $this->doMinutesOrSecondsOverflow($dateTime)) {
            throw new InvalidDateTimeException(
                sprintf(
                    'The provided time should be between %s and %s hours, %s provided!',
                    WorkintTimeConfig::START_TIME_IN_HOURS,
                    WorkintTimeConfig::END_TIME_IN_HOURS,
                    $dateTime->format('H:i'),
                ),
            );
        }
    }

    private function isHourValid(\DateTimeInterface $dateTime): bool
    {
        $hours = (int) $dateTime->format('G');

        return WorkintTimeConfig::END_TIME_IN_HOURS >= $hours && WorkintTimeConfig::START_TIME_IN_HOURS <= $hours;
    }

    private function doMinutesOrSecondsOverflow(\DateTimeInterface $dateTime): bool
    {
        return WorkintTimeConfig::END_TIME_IN_HOURS === (int) $dateTime->format('G')
            && (0 !== (int) $dateTime->format('i') || 0 !== (int) $dateTime->format('s'));
    }
}
