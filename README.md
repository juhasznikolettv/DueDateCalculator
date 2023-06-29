# Due Date Calculator

## What is this project?

This project was created to provide a solution to implement a due date calculator in an issue tracking
system. The calculator counts the days only from monday to friday and working hours starting from 9:00 to 17:00.

## Requirement

- php7.4 (or newer)
- installed composer

## Installation

Pull the repository and run this command:

```
composer up
```

## Run tests

If you have already installed phpunit globally, you can run it this way:

```
phpunit
```

otherwise after the composer up you can run the tests from the project root directory with:

```
./vendor/bin/phpunit
```

(use --testdox option for some extra fun :) )

## Usage

You'll have a `JNV\DueDateCalculator\Calculator\DueDateCalculator` class, with this public method:

```
public function calculateDueDate(DateTimeInterface $submitDate, int $turnAroundHours): DateTimeInterface
```

#### Parameters:

**$submitDate** *(DateTimeInterface)*

This parameteres will be the date and time, from where the calculator starts counting the hours. 
Please be aware: this parameter can be any DateTimeInterface (DateTime, DateTimeImmutable etc).

**$turnAroundHours** *(int)*

The amount of time that the task takes, expressed in hours. (Must be a positive integer, higher than zero.)

#### Return:

**DateTimeInterface**

The expected finish date for the task.

#### Throws:

**InvalidDateDayException**

The submitDate does not contain a valid working day, it might be saturday or sunday.

**InvalidDateTimeException**

The submitDate does not contain a valid working hour, it might be less than 9:00 or more than 17:00.

**InvalidTurnaroundHoursException**

The provided turnAroundHours parameter is not a positive integer.


## Finishing touches

Before you commit, please make sure that your code is aligned with the coding standards used by this project! 
Please add some code-formatting magic: 

```
./vendor/bin/php-cs-fixer fix
```

If you are using a higher version of PHP (above 8.0.*), you should run

```
 PHP_CS_FIXER_IGNORE_ENV=1 ./vendor/bin/php-cs-fixer fix
```