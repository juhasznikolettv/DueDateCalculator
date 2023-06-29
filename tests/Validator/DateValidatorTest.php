<?php

declare(strict_types=1);

namespace JNV\Tests\DueDateCalculator\Validator;

use JNV\DueDateCalculator\Exception\AbstractInvalidDateException;
use JNV\DueDateCalculator\Util\WorkintTimeConfig;
use JNV\DueDateCalculator\Validator\DateDayValidator;
use JNV\DueDateCalculator\Validator\DateTimeValidator;
use JNV\DueDateCalculator\Validator\DateValidator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DateValidatorTest extends TestCase
{
    /**
     * @dataProvider dataProviderForValidData
     * @doesNotPerformAssertions
     */
    public function testValidateRunsWithoutExceptionWithValidData(\DateTime $dateTime): void
    {
        $dayValidator = new DateDayValidator();
        $timeValidator = new DateTimeValidator();
        $validator = new DateValidator([$dayValidator, $timeValidator]);

        $validator->validate($dateTime);
    }

    public static function dataProviderForValidData(): iterable
    {
        return [
            'wednesday morning' => [
                'date' => new \DateTime('2023-06-21 09:30:00'),
            ],
            'wednesday evening' => [
                'date' => new \DateTime('2023-06-07 17:00:00'),
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForInvalidData
     *
     * @throws AbstractInvalidDateException
     */
    public function testValidateThrowsExceptionWithInvalidData(\DateTime $dateTime, string $expectedExceptionMessage): void
    {
        $dayValidator = new DateDayValidator();
        $timeValidator = new DateTimeValidator();
        $validator = new DateValidator([$dayValidator, $timeValidator]);

        $this->expectException(AbstractInvalidDateException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $validator->validate($dateTime);
    }

    public static function dataProviderForInvalidData(): iterable
    {
        return [
            'saturday morning' => [
                'date' => $dateTime = new \DateTime('2022-09-17 09:30:00'),
                'expectedExceptionMessage' => sprintf(
                    'Provided date should be a weekday, %s provided!',
                    $dateTime->format('l')
                ),
            ],
            'sunday evening' => [
                'date' => $dateTime = new \DateTime('2023-07-02 17:00:00'),
                'expectedExceptionMessage' => sprintf(
                    'Provided date should be a weekday, %s provided!',
                    $dateTime->format('l')
                ),
            ],
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
        ];
    }
}
