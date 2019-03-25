<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Result;

use Closure;
use ReflectionFunction;

/**
 * @param callable $f with signature (T1 $a1, T2 $a2, ...) ->  T
 * @return callable with signature (ValidationResult T1, ValidationResult T2, ...) -> ValidationResult T
 */
function lift(callable $f): callable
{
    // retrieve the number of arguments of $f
    $fClosure = Closure::fromCallable($f);
    $fRef = new ReflectionFunction($fClosure);
    $fParamsCount = $fRef->getNumberOfParameters();

    if ($fParamsCount > 1) {
    }

    return function (ValidationResult $validatedArgument) use ($f) {
        return $validatedArgument->map($f);
    };
}

function do_(): callable
{
}
