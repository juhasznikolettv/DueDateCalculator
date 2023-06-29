<?php

declare(strict_types=1);

namespace JNV\Tests\DueDateCalculator\Calculator;

use JNV\DueDateCalculator\Calculator\DueDateCalculator;
use JNV\DueDateCalculator\Exception\AbstractInvalidDateException;
use JNV\DueDateCalculator\Exception\InvalidTurnaroundHoursException;
use JNV\DueDateCalculator\Util\WorkintTimeConfig;
use JNV\DueDateCalculator\Validator\DateDayValidator;
use JNV\DueDateCalculator\Validator\DateTimeValidator;
use JNV\DueDateCalculator\Validator\DateValidator;
use JNV\DueDateCalculator\Validator\DateValidatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DueDateCalculatorTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     */
    public function testCalculateDueDateWithValidInputData(
        \DateTimeInterface $submitDate,
        int $turnaroundHours,
        \DateTimeInterface $expectedOutput
    ): void {
        $calculator = new DueDateCalculator($this->getValidator());

        $result = $calculator->calculateDueDate($submitDate, $turnaroundHours);

        $this->assertEquals($expectedOutput, $result);
    }

    public static function validDataProvider(): iterable
    {
        return [
            'add 1 hour to 9:00' => [
                    'submitDate' => new \DateTime('2023-06-28 09:00:00'),
                    'turnaroundHours' => 1,
                    'expectedOutput' => new \DateTime('2023-06-28 10:00:00'),
                ],
            'add 1 hour to 16:00' => [
                    'submitDate' => new \DateTime('2023-06-28 16:00:00'),
                    'turnaroundHours' => 1,
                    'expectedOutput' => new \DateTime('2023-06-28 17:00:00'),
                ],
            'add 1 hour to 16:01' => [
                    'submitDate' => new \DateTime('2023-06-28 16:01:00'),
                    'turnaroundHours' => 1,
                    'expectedOutput' => new \DateTime('2023-06-29 09:01:00'),
                ],
            'add 9 hours to 9:11' => [
                    'submitDate' => new \DateTime('2023-06-28 09:11:00'),
                    'turnaroundHours' => 9,
                    'expectedOutput' => new \DateTime('2023-06-29 10:11:00'),
                ],
            'add 8 hours to 9:00' => [
                    'submitDate' => new \DateTime('2023-06-28 09:00:00'),
                    'turnaroundHours' => 8,
                    'expectedOutput' => new \DateTime('2023-06-29 09:00:00'),
                ],
            'add a week (40 hours) to 15:00' => [
                    'submitDate' => new \DateTime('2023-06-28 15:00:00'),
                    'turnaroundHours' => 40,
                    'expectedOutput' => new \DateTime('2023-07-05 15:00:00'),
                ],
            'add a week and a day (48 hours) to 11:30' => [
                    'submitDate' => new \DateTime('2023-06-28 11:30:00'),
                    'turnaroundHours' => 48,
                    'expectedOutput' => new \DateTime('2023-07-06 11:30:00'),
                ],
            'add 38 day (304 hours) to 16:30' => [
                    'submitDate' => new \DateTime('2023-06-28 16:30:00'),
                    'turnaroundHours' => 304,
                    'expectedOutput' => new \DateTime('2023-08-21 16:30:00'),
                ],
            'add 2 hours to 16:30' => [
                    'submitDate' => new \DateTime('2023-06-28 16:30:00'),
                    'turnaroundHours' => 2,
                    'expectedOutput' => new \DateTime('2023-06-29 10:30:00'),
                ],
            'add 1 hour to 17:00' => [
                    'submitDate' => new \DateTime('2023-06-28 17:00:00'),
                    'turnaroundHours' => 1,
                    'expectedOutput' => new \DateTime('2023-06-29 10:00:00'),
                ],
        ];
    }

    /**
     * @dataProvider invalidTurnaroundHoursProvider
     *
     * @throws InvalidTurnaroundHoursException
     */
    public function testCalculateDueDateWithInvalidTurnaroundHours(int $hours, string $expectedExceptionMessage): void
    {
        $submitDate = new \DateTime('2023-06-26 09:30:00');
        $calculator = new DueDateCalculator($this->getValidator());

        $this->expectException(InvalidTurnaroundHoursException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $calculator->calculateDueDate($submitDate, $hours);
    }

    public static function invalidTurnaroundHoursProvider(): iterable
    {
        return [
            'zero turnaround hours' => [
                'hours' => $turnAroundHours = 0,
                'expectedExceptionMessage' => sprintf(
                    'Turnaround hours should be a positive integer, %s provided',
                    $turnAroundHours
                ),
            ],
            'negative turnaround hours' => [
                'hours' => $turnAroundHours = -3,
                'expectedExceptionMessage' => sprintf(
                    'Turnaround hours should be a positive integer, %s provided',
                    $turnAroundHours
                ),
            ],
        ];
    }

    /**
     * @dataProvider invalidSubmitDateProvider
     *
     * @throws AbstractInvalidDateException
     */
    public function testCalculateDueDateWithInvalidSubmitDate(\DateTimeInterface $submitDate, string $expectedExceptionMessage): void
    {
        $calculator = new DueDateCalculator($this->getValidator());

        $this->expectException(AbstractInvalidDateException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $calculator->calculateDueDate($submitDate, 1);
    }

    public static function invalidSubmitDateProvider(): iterable
    {
        return [
            'too early' => [
                'submitDate' => $dateTime = new \DateTime('2023-06-21 04:30:00'),
                'expectedExceptionMessage' => sprintf(
                    'The provided time should be between %s and %s hours, %s provided!',
                    WorkintTimeConfig::START_TIME_IN_HOURS,
                    WorkintTimeConfig::END_TIME_IN_HOURS,
                    $dateTime->format('H:i'),
                ),
            ],
            'too late' => [
                'submitDate' => $dateTime = new \DateTime('2023-06-21 21:10:00'),
                'expectedExceptionMessage' => sprintf(
                    'The provided time should be between %s and %s hours, %s provided!',
                    WorkintTimeConfig::START_TIME_IN_HOURS,
                    WorkintTimeConfig::END_TIME_IN_HOURS,
                    $dateTime->format('H:i'),
                ),
            ],
            'too early on sunday' => [
                'submitDate' => $dateTime = new \DateTime('2023-06-11 04:30:00'),
                'expectedExceptionMessage' => sprintf(
                    'Provided date should be a weekday, %s provided!',
                    $dateTime->format('l')
                ),
            ],
            'in time on saturday' => [
                'submitDate' => $dateTime = new \DateTime('2023-06-10 10:01:00'),
                'expectedExceptionMessage' => sprintf(
                    'Provided date should be a weekday, %s provided!',
                    $dateTime->format('l')
                ),
            ],
        ];
    }

    private function getValidator(): DateValidatorInterface
    {
        return new DateValidator(
            [
                new DateDayValidator(),
                new DateTimeValidator(),
            ]
        );
    }
}
