<?php

declare(strict_types=1);

namespace JNV\DueDateCalculator\Validator;

class DateValidator implements DateValidatorInterface
{
    /**
     * @var DateValidatorInterface[]
     */
    private array $validators = [];

    public function __construct(iterable $validators)
    {
        foreach ($validators as $validator) {
            if ($validator instanceof DateValidatorInterface) {
                $this->validators[] = $validator;
            }
        }
    }

    public function validate(\DateTimeInterface $dateTime): void
    {
        foreach ($this->validators as $validator) {
            $validator->validate($dateTime);
        }
    }
}
