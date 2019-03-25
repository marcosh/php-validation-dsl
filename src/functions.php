<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL;

use Closure;
use ReflectionFunction;

/**
 * implementation heavily inspired by https://github.com/m4rw3r/autoCurry
 *
 * @param callable $f
 * @return callable
 */
function curry(callable $f): callable
{
    $innerCurry = static function (
        callable $f,
        ?int $numberOfParameters = null,
        array $parameters = []
    ) use (&$innerCurry): callable {
        if (null === $numberOfParameters) {
            // retrieve number of parameters from reflection
            $fClosure = Closure::fromCallable($f);
            $fRef = new ReflectionFunction($fClosure);
            $numberOfParameters = $fRef->getNumberOfParameters();
        }

        return static function () use ($f, $numberOfParameters, $parameters, $innerCurry) {
            /** @var array<int, mixed> $newParameters */
            $newParameters = array_merge($parameters, func_get_args());

            if (count($newParameters) >= $numberOfParameters) {
                return call_user_func_array($f, $newParameters);
            }

            return $innerCurry($f, $numberOfParameters, $newParameters);
        };
    };

    return $innerCurry($f);
}

/**
 * @param callable $f with signature $a1 -> ($a2 -> (... -> $something))
 * @return callable with signature ($a1, $a2, ...) -> $something
 */
function uncurry(callable $f): callable
{
    return function (...$params) use ($f) {
        if ([] === $params) {
            return $f;
        }

        $firstParam = $params[0];

        return uncurry($f($firstParam))(array_slice($params, 1));
    };
}
