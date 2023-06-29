<?php

declare(strict_types=1);

namespace JNV\Tests\DueDateCalculator\Validator;

use JNV\DueDateCalculator\Exception\InvalidDateDayException;
use JNV\DueDateCalculator\Validator\DateDayValidator;
use PHPUnit\Framework\TestCase;

class DateDayValidatorTest extends TestCase
{
    /**
     * @dataProvider dataProviderForValidData
     * @doesNotPerformAssertions
     */
    public function testValidateRunsWithoutExceptionWithValidData(\DateTime $dateTime): void
    {
        $validator = new DateDayValidator();

        $validator->validate($dateTime);
    }

    public static function dataProviderForValidData(): iterable
    {
        return [
            'monday morning' => [
                'date' => new \DateTime('2023-06-26 09:30:00'),
            ],
            'friday evening' => [
                'date' => new \DateTime('2023-02-24 17:00:00'),
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForInvalidData
     *
     * @throws InvalidDateDayException
     */
    public function testValidateThrowsExceptionWithInvalidData(\DateTime $dateTime, string $expectedExceptionMessage): void
    {
        $validator = new DateDayValidator();

        $this->expectException(InvalidDateDayException::class);
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
        ];
    }
}
