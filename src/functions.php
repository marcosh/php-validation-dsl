<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL;

use Closure;
use ReflectionFunction;

/**
 * implementation heavily inspired by https://github.com/m4rw3r/autoCurry
 *
 * @param callable $f with signature ($a1, $a2, ...) -> $something
 * @return Closure with signature $a1 -> ($a2 -> (... -> $something))
 *
 * @psalm-return Closure(): callable
 */
function curry(callable $f): Closure
{
    $innerCurry =
        /**
         * @return Closure
         *
         * @psalm-return Closure(): callable
         */
        static function (
            callable $f,
            ?int $numberOfParameters = null,
            array $parameters = []
        ) use (&$innerCurry): Closure {
            if (null === $numberOfParameters) {
                // retrieve number of parameters from reflection
                $fClosure = Closure::fromCallable($f);
                $fRef = new ReflectionFunction($fClosure);
                $numberOfParameters = $fRef->getNumberOfParameters();
            }

            /** @psalm-suppress MissingClosureReturnType */
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
 * @return Closure with signature ($a1, $a2, ...) -> $something
 *
 * @psalm-return Closure(... array<int, mixed>): mixed
 */
function uncurry(callable $f): Closure
{
    /** @psalm-suppress MissingClosureReturnType */
    return static function (...$params) use ($f) {
        if ([] === $params) {
            return $f();
        }

        $firstParam = $params[0];

        $firstApplication = $f($firstParam);

        if (! is_callable($firstApplication)) {
            return $firstApplication;
        }

        return uncurry($firstApplication)(...array_slice($params, 1));
    };
}
