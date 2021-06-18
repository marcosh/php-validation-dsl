<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;

/**
 * @template A
 */
abstract class Compare
{
    public const MESSAGE = 'composing-bound.not-respecting-bound';

    /** @var A could be any comparable type */
    protected $comparisonBasis;

    /** @var callable(A, A): string[] */
    private $errorFormatter;

    /**
     * @param A $comparisonBasis
     * @param callable(A, A): string[] $errorFormatter
     */
    private function __construct($comparisonBasis, callable $errorFormatter)
    {
        $this->comparisonBasis = $comparisonBasis;
        $this->errorFormatter = $errorFormatter;
    }

    /**
     * @template B
     * @param B $comparisonBasis
     * @return Compare<B>
     */
    public static function withBound($comparisonBasis): self
    {
        return self::withBoundAndFormatter(
            $comparisonBasis,
            /**
             * @param B $comparisonBasis
             * @param B $data
             * @return string[]
             */
            function ($comparisonBasis, array $data): array {
                /** @var string $message */
                $message = static::MESSAGE;

                return [$message];
            }
        );
    }

    /**
     * @template B
     * @param B $comparisonBasis
     * @param callable(B, B): string[] $errorFormatter
     * @return Compare<B>
     */
    public static function withBoundAndFormatter($comparisonBasis, callable $errorFormatter): self
    {
        /** @psalm-suppress UnsafeInstantiation */
        return new static($comparisonBasis, $errorFormatter);
    }

    /**
     * @template B
     * @param B $comparisonBasis
     * @param Translator $translator
     * @return Compare<B>
     */
    public static function withBoundAndTranslator($comparisonBasis, Translator $translator): self
    {
        return self::withBoundAndFormatter(
            $comparisonBasis,
            /**
             * @param B $comparisonBasis
             * @param B $data
             * @return string[]
             */
            function ($comparisonBasis, $data) use ($translator): array {
                /** @var string $message */
                $message = static::MESSAGE;

                return [$translator->translate($message)];
            }
        );
    }

    /**
     * @param A $data
     * @return ValidationResult<A>
     */
    abstract public function validate($data, array $context = []): ValidationResult;

    /**
     * @param callable(A, A):bool $assertion
     * @param A $data
     * @return ValidationResult<A>
     */
    protected function validateAssertion(callable $assertion, $data, array $context = []): ValidationResult
    {
        $comparisonBasis = $this->comparisonBasis;
        $errorFormatter = $this->errorFormatter;

        return IsAsAsserted::withAssertionAndErrorFormatter(
            /**
             * @param A $data
             */
            function ($data) use ($comparisonBasis, $assertion): bool {
                return $assertion($comparisonBasis, $data);
            },
            /**
             * @param A $data
             */
            function ($data) use ($comparisonBasis, $errorFormatter): array {
                return $errorFormatter($comparisonBasis, $data);
            }
        )->validate($data, $context);
    }
}
