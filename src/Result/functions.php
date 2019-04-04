<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Result;

use Closure;
use function Marcosh\PhpValidationDSL\curry;
use ReflectionFunction;
use Webmozart\Assert\Assert;

/**
 * @param callable $f with signature (T1 $a1, T2 $a2, ...) ->  T
 * @return Closure with signature (ValidationResult T1, ValidationResult T2, ...) -> ValidationResult T
 *
 * @psalm-return Closure(): ValidationResult
 */
function lift(callable $f): Closure
{
    $innerLift =
        /**
         * @return Closure
         *
         * @psalm-return Closure(): ValidationResult
         */
        static function (
            callable $f,
            ?int $numberOfParameters = null,
            array $parameters = []
        ) use (&$innerLift): Closure {
            if (null === $numberOfParameters) {
                // retrieve number of parameters from reflection
                $fClosure = Closure::fromCallable($f);
                $fRef = new ReflectionFunction($fClosure);
                $numberOfParameters = $fRef->getNumberOfParameters();
            }

            /** @return ValidationResult|Closure */
            /** @psalm-suppress MissingClosureReturnType */
            return static function () use ($f, $numberOfParameters, $parameters, $innerLift) {
                // collect all necessary parameters

                /** @var array<int, mixed> $newParameters */
                $newParameters = array_merge($parameters, func_get_args());

                if (count($newParameters) >= $numberOfParameters) {
                    if ([] === $newParameters) {
                        return ValidationResult::valid($f());
                    }

                    $firstParameter = $newParameters[0];

                    $result = $firstParameter->map(curry($f));

                    foreach (array_slice($newParameters, 1) as $validatedParameter) {
                        $result = $validatedParameter->apply($result);
                    }

                    return $result;
                }

                return $innerLift($f, $numberOfParameters, $newParameters);
            };
        };

    return $innerLift($f);
}

/**
 * @param callable[] $fs every function takes as arguments the unwrapped results of the previous one and returns a
 *                      ValidationResult
 * @return ValidationResult
 */
function do_(callable ...$fs): ValidationResult
{
    Assert::notEmpty($fs, 'do_ must receive at least one callable');

    $result = ValidationResult::valid(null);

    foreach ($fs as $f) {
        $result = $result->bind($f);
    }

    return $result;
}
