<?php

declare(strict_types=1);

namespace JNV\DueDateCalculator\Validator;

use JNV\DueDateCalculator\Exception\AbstractInvalidDateException;

interface DateValidatorInterface
{
    /**
     * @throws AbstractInvalidDateException
     */
    public function validate(\DateTimeInterface $dateTime): void;
}
