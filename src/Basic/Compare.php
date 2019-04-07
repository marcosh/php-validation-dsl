<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;

/**
 * @template B
 */
abstract class Compare
{
    public const MESSAGE = 'composing-bound.not-respecting-bound';

    /** @var mixed could be any comparable type */
    protected $comparisonBasis;

    /**
     * @var callable with signature $comparisonBasis -> $data -> string[]
     */
    private $errorFormatter;

    /**
     * @param mixed $comparisonBasis
     * @psalm-param B $comparisonBasis
     * @param callable $errorFormatter
     */
    private function __construct($comparisonBasis, callable $errorFormatter)
    {
        $this->comparisonBasis = $comparisonBasis;
        $this->errorFormatter = $errorFormatter;
    }

    /**
     * @psalm-param B $comparisonBasis
     * @param mixed $comparisonBasis
     * @return Compare
     */
    public static function withBound($comparisonBasis): self
    {
        return self::withBoundAndFormatter(
            $comparisonBasis,
            /**
             * @param mixed $comparisonBasis
             * @psalm-param B $comparisonBasis
             * @param mixed $data
             * @psalm-param B $data
             * @return string[]
             * @psalm-return array{0:mixed}
             */
            function ($comparisonBasis, $data): array {

                return [static::MESSAGE];
            }
        );
    }

    /**
     * @param mixed $comparisonBasis
     * @psalm-param B $comparisonBasis
     * @param callable $errorFormatter
     * @return Compare
     */
    public static function withBoundAndFormatter($comparisonBasis, callable $errorFormatter): self
    {
        return new static($comparisonBasis, $errorFormatter);
    }

    /**
     * @param mixed $comparisonBasis
     * @psalm-param B $comparisonBasis
     * @param Translator $translator
     * @return Compare
     */
    public static function withBoundAndTranslator($comparisonBasis, Translator $translator): self
    {
        return self::withBoundAndFormatter(
            $comparisonBasis,
            /**
             * @param mixed $comparisonBasis
             * @psalm-param B $comparisonBasis
             * @param mixed $data
             * @psalm-param B $data
             * @return string[]
             * @psalm-return array{0:mixed}
             */
            function ($comparisonBasis, $data) use ($translator): array {
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
     * @psalm-param callable(B, B):bool $assertion
     * @param mixed $data
     * @psalm-param B $data
     * @param array $context
     * @return ValidationResult
     */
    protected function validateAssertion(callable $assertion, $data, array $context = []): ValidationResult
    {
        $comparisonBasis = $this->comparisonBasis;
        $errorFormatter = $this->errorFormatter;

        return IsAsAsserted::withAssertionAndErrorFormatter(
            /**
             * @param mixed $data
             * @psalm-param B $data
             */
            function ($data) use ($comparisonBasis, $assertion): bool {
                return $assertion($comparisonBasis, $data);
            },
            /**
             * @param mixed $data
             * @psalm-param B $data
             */
            function ($data) use ($comparisonBasis, $errorFormatter): array {
                return $errorFormatter($comparisonBasis, $data);
            }
        )->validate($data, $context);
    }
}
