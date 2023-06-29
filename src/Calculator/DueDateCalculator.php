<?php

declare(strict_types=1);

namespace JNV\DueDateCalculator\Calculator;

use DateTimeInterface;
use JNV\DueDateCalculator\Exception\AbstractInvalidDateException;
use JNV\DueDateCalculator\Exception\InvalidTurnaroundHoursException;
use JNV\DueDateCalculator\Util\WorkintTimeConfig;
use JNV\DueDateCalculator\Validator\DateValidatorInterface;

class DueDateCalculator
{
    private DateValidatorInterface $submitDateValidator;

    public function __construct(DateValidatorInterface $submitDateValidator)
    {
        $this->submitDateValidator = $submitDateValidator;
    }

    public function calculateDueDate(DateTimeInterface $submitDate, int $turnAroundHours): DateTimeInterface
    {
        $this->validateInputData($submitDate, $turnAroundHours);

        $resultDate = $submitDate;

        $leftOverHours = $this->addWholeDaysToSubmitDate($resultDate, $turnAroundHours);

        if (0 === $leftOverHours) {
            return $resultDate;
        }

        return $this->addRemainingHoursToSubmitDate($resultDate, $leftOverHours);
    }

    private function validateInputData(DateTimeInterface $submitDate, int $turnAroundHours): void
    {
        $this->submitDateValidator->validate($submitDate);

        if (0 >= $turnAroundHours) {
            throw new InvalidTurnaroundHoursException(
                sprintf(
                     'Turnaround hours should be a positive integer, %s provided',
                     $turnAroundHours
                 )
            );
        }
    }

    private function addWholeDaysToSubmitDate(DateTimeInterface $submitDate, int $turnAroundHours): int
    {
        $wholeDaysCount = intdiv($turnAroundHours, WorkintTimeConfig::WORKING_HOUR_COUNT_IN_A_DAY);

        $submitDate->modify("+{$wholeDaysCount} weekdays");

        return $turnAroundHours % WorkintTimeConfig::WORKING_HOUR_COUNT_IN_A_DAY;
    }

    private function addRemainingHoursToSubmitDate(DateTimeInterface $resultDate, int $remainingHours): DateTimeInterface
    {
        $resultHour = (int) $resultDate->format('H');
        $workingDayEndingHour = $this->calculateEndingHour($resultDate);

        for ($i = 0; $i < $remainingHours; ++$i) {
            if (
                WorkintTimeConfig::END_TIME_IN_HOURS === $resultHour
                && WorkintTimeConfig::END_TIME_IN_HOURS === $workingDayEndingHour
            ) {
                $this->increaseToNextDay($resultDate);
            }

            try {
                $this->submitDateValidator->validate($resultDate);
                $resultDate->modify('+1 hour');
                $this->submitDateValidator->validate($resultDate);
            } catch (AbstractInvalidDateException $exception) {
                $this->increaseToNextDay($resultDate);
            }
        }

        return $resultDate;
    }

    private function calculateEndingHour(DateTimeInterface $resultDate): int
    {
        return 0 === (int) $resultDate->format('i') && 0 === (int) $resultDate->format('s')
            ? WorkintTimeConfig::END_TIME_IN_HOURS
            : WorkintTimeConfig::END_TIME_IN_HOURS - 1;
    }

    private function increaseToNextDay(DateTimeInterface $resultDate): void
    {
        $resultDate->modify('+1 weekday');
        $resultDate->setTime(
            WorkintTimeConfig::START_TIME_IN_HOURS,
            (int) $resultDate->format('i'),
            (int) $resultDate->format('s')
        );
    }
}
