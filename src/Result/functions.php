<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Result;

use Closure;
use ReflectionFunction;
use Webmozart\Assert\Assert;

use function Marcosh\PhpValidationDSL\curry;

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
function sdo(callable ...$fs): ValidationResult
{
    Assert::notEmpty($fs, 'do_ must receive at least one callable');

    $result = ValidationResult::valid(null);

    foreach ($fs as $f) {
        $result = $result->bind($f);
    }

    return $result;
}

final class DoPartialTempResult
{
    /**
     * @var callable
     */
    private $f;

    /**
     * @var array
     */
    private $arguments;

    private function __construct(callable $f, array $arguments)
    {
        $this->f = $f;
        $this->arguments = $arguments;
    }

    public static function fromCallableAndArguments(callable $f, array $arguments): ValidationResult
    {
        return ValidationResult::valid(new self($f, $arguments));
    }

    public static function fromPreviousAndCallable(ValidationResult $previous, callable $f): ValidationResult
    {
        return $previous->bind(function (DoPartialTempResult $doPartialTempResult) use ($f): ValidationResult {
            $lastArgumentResult = $doPartialTempResult();

            /** @psalm-suppress MissingClosureParamType */
            return $lastArgumentResult->bind(function ($lastArgument) use ($doPartialTempResult, $f): ValidationResult {
                $fArguments = array_merge($doPartialTempResult->arguments, [$lastArgument]);

                return self::fromCallableAndArguments($f, $fArguments);
            });
        });
    }

    public function __invoke(): ValidationResult
    {
        return call_user_func_array($this->f, $this->arguments);
    }
}

/**
 * @param callable[] $fs every function takes as arguments the unwrapped results of the previous one and returns a
 *                      ValidationResult
 * @return ValidationResult
 */
function mdo(callable ...$fs): ValidationResult
{
    Assert::notEmpty($fs, 'do__ must receive at least one callable');

    $doPartialTempResult = DoPartialTempResult::fromCallableAndArguments($fs[0], []);

    foreach (array_slice($fs, 1) as $f) {
        $doPartialTempResult = DoPartialTempResult::fromPreviousAndCallable($doPartialTempResult, $f);
    }

    return $doPartialTempResult->bind(function (DoPartialTempResult $doPartialTempResult): ValidationResult {
        return $doPartialTempResult();
    });
}
