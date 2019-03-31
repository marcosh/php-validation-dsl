<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Result;

use Closure;
use function Marcosh\PhpValidationDSL\curry;
use ReflectionFunction;

/**
 * @param callable $f with signature (T1 $a1, T2 $a2, ...) ->  T
 * @return callable with signature (ValidationResult T1, ValidationResult T2, ...) -> ValidationResult T
 */
function lift(callable $f): callable
{
    $innerLift = static function (
        callable $f,
        ?int $numberOfParameters = null,
        array $parameters = []
    ) use (&$innerLift): callable {
        if (null === $numberOfParameters) {
            // retrieve number of parameters from reflection
            $fClosure = Closure::fromCallable($f);
            $fRef = new ReflectionFunction($fClosure);
            $numberOfParameters = $fRef->getNumberOfParameters();
        }

        return static function () use ($f, $numberOfParameters, $parameters, $innerLift) {
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

function do_(): callable
{
}
