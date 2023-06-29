<?php

declare(strict_types=1);

namespace JNV\Tests\DueDateCalculator\Validator;

use JNV\DueDateCalculator\Exception\InvalidDateTimeException;
use JNV\DueDateCalculator\Util\WorkintTimeConfig;
use JNV\DueDateCalculator\Validator\DateTimeValidator;
use PHPUnit\Framework\TestCase;

class DateTimeValidatorTest extends TestCase
{
    /**
     * @dataProvider dataProviderForValidData
     * @doesNotPerformAssertions
     */
    public function testValidateRunsWithoutExceptionWithValidData(\DateTime $dateTime): void
    {
        $validator = new DateTimeValidator();

        $validator->validate($dateTime);
    }

    public static function dataProviderForValidData(): iterable
    {
        return [
            'starting time' => [
                'date' => new \DateTime('2023-06-26 09:00:00'),
            ],
            'middle of the day' => [
                'date' => new \DateTime('2023-07-01 12:00:00'),
            ],
            'the end of the day' => [
                'date' => new \DateTime('2023-02-24 17:00:00'),
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForInvalidData
     *
     * @throws InvalidDateTimeException
     */
    public function testValidateThrowsExceptionWithInvalidData(\DateTime $dateTime, string $expectedExceptionMessage): void
    {
        $validator = new DateTimeValidator();

        $this->expectException(InvalidDateTimeException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $validator->validate($dateTime);
    }

    public static function dataProviderForInvalidData(): iterable
    {
        return [
            'too early' => [
                'date' => $dateTime = new \DateTime('2022-09-12 08:30:00'),
                'expectedExceptionMessage' => sprintf(
                    'The provided time should be between %s and %s hours, %s provided!',
                    WorkintTimeConfig::START_TIME_IN_HOURS,
                    WorkintTimeConfig::END_TIME_IN_HOURS,
                    $dateTime->format('H:i'),
                ),
            ],
            'too late' => [
                'date' => $dateTime = new \DateTime('2023-07-03 18:00:00'),
                'expectedExceptionMessage' => sprintf(
                    'The provided time should be between %s and %s hours, %s provided!',
                    WorkintTimeConfig::START_TIME_IN_HOURS,
                    WorkintTimeConfig::END_TIME_IN_HOURS,
                    $dateTime->format('H:i'),
                ),
            ],
            'late by a few minutes' => [
                'date' => $dateTime = new \DateTime('2023-06-12 17:12:00'),
                'expectedExceptionMessage' => sprintf(
                    'The provided time should be between %s and %s hours, %s provided!',
                    WorkintTimeConfig::START_TIME_IN_HOURS,
                    WorkintTimeConfig::END_TIME_IN_HOURS,
                    $dateTime->format('H:i'),
                ),
            ],
            'late by a few seconds' => [
                'date' => $dateTime = new \DateTime('2023-06-12 17:00:03'),
                'expectedExceptionMessage' => sprintf(
                    'The provided time should be between %s and %s hours, %s provided!',
                    WorkintTimeConfig::START_TIME_IN_HOURS,
                    WorkintTimeConfig::END_TIME_IN_HOURS,
                    $dateTime->format('H:i'),
                ),
            ],
        ];
    }
}
