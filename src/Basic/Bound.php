<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Closure;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;

/**
 * @template B
 */
abstract class Bound
{
    public const MESSAGE = 'composing-bound.not-respecting-bound';

    /** @var mixed could be any comparable type */
    protected $bound;

    /**
     * @var callable with signature $bound -> $data -> string[]
     */
    private $errorFormatter;

    /**
     * @param mixed $bound
     * @psalm-param B $bound
     * @param callable $errorFormatter
     */
    private function __construct($bound, callable $errorFormatter)
    {
        $this->bound = $bound;
        $this->errorFormatter = $errorFormatter;
    }

    /**
     * @psalm-param B $bound
     * @param mixed $bound
     * @return Bound
     */
    public static function withBound($bound): self
    {
        return self::withBoundAndFormatter(
            $bound,
            /**
             * @param mixed $bound
             * @psalm-param B $bound
             * @param mixed $data
             * @psalm-param B $data
             * @return string[]
             * @psalm-return array{0:mixed}
             */
            function ($bound, $data): array {

                return [static::MESSAGE];
            }
        );
    }

    /**
     * @param mixed $bound
     * @psalm-param B $bound
     * @param callable $errorFormatter
     * @return Bound
     */
    public static function withBoundAndFormatter($bound, callable $errorFormatter): self
    {
        return new static($bound, $errorFormatter);
    }

    /**
     * @param mixed $bound
     * @psalm-param B $bound
     * @param Translator $translator
     * @return Bound
     */
    public static function withBoundAndTranslator($bound, Translator $translator): self
    {
        return self::withBoundAndFormatter(
            $bound,
            /**
             * @param mixed $bound
             * @psalm-param B $bound
             * @param mixed $data
             * @psalm-param B $data
             * @return string[]
             * @psalm-return array{0:mixed}
             */
            function ($bound, $data) use ($translator): array {
                return [$translator->translate(static::MESSAGE)];
            }
        );
    }

    /**
     * @param mixed $data
     * @param array $context
     * @return ValidationResult
     */
    abstract public function validate($data, array $context = []): ValidationResult;

    /**
     * @param callable $assertion
     * @psalm-param Closure(B, B): bool $assertion
     * @param mixed $data
     * @psalm-param B $data
     * @param array $context
     * @return ValidationResult
     */
    protected function validateAssertion(callable $assertion, $data, array $context = []): ValidationResult
    {
        $bound = $this->bound;
        $errorFormatter = $this->errorFormatter;

        return IsAsAsserted::withAssertionAndErrorFormatter(
            /**
             * @param mixed $data
             * @psalm-param B $data
             */
            function ($data) use ($bound, $assertion): bool {
                return $assertion($bound, $data);
            },
            /**
             * @param mixed $data
             * @psalm-param B $data
             */
            function ($data) use ($bound, $errorFormatter): array {
                return $errorFormatter($bound, $data);
            }
        )->validate($data, $context);
    }
}
