<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use _HumbugBoxa991b62ce91e\React\Http\Io\Transaction;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

/**
 * @template A
 * @implements Validation<A, A>
 */
final class IsAsAsserted implements Validation
{
    public const NOT_AS_ASSERTED = 'is-as-asserted.not-as-asserted';

    /** @var callable(A): bool */
    private $assertion;

    /** @var callable(A): string[] */
    private $errorFormatter;

    /**
     * @param callable(A): bool $assertion
     * @param null|callable(A): string[] $errorFormatter
     */
    private function __construct(callable $assertion, ?callable $errorFormatter = null)
    {
        $this->assertion = $assertion;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            /**
             * @param A $data
             * @return string[]
             */
            function ($data): array {
                return [self::NOT_AS_ASSERTED];
            };
    }

    /**
     * @template B
     * @param callable(B): bool $assertion
     * @return self<B>
     */
    public static function withAssertion(callable $assertion): self
    {
        return new self($assertion);
    }

    /**
     * @template B
     * @param callable(B): bool $assertion
     * @param callable(B): string[] $errorFormatter
     * @return self<B>
     */
    public static function withAssertionAndErrorFormatter(callable $assertion, callable $errorFormatter): self
    {
        return new self($assertion, $errorFormatter);
    }

    /**
     * @template B
     * @param callable(B): bool $assertion
     * @param Translator $translator
     * @return self<B>
     */
    public static function withAssertionAndTranslator(callable $assertion, Translator $translator): self
    {
        return new self(
            $assertion,
            /**
             * @param A $data
             * @return string[]
             */
            function ($data) use ($translator): array {
                return [$translator->translate(self::NOT_AS_ASSERTED)];
            }
        );
    }

    /**
     * @param A $data
     * @return ValidationResult<A>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        if (! ($this->assertion)($data)) {
            return ValidationResult::errors(($this->errorFormatter)($data));
        }

        return ValidationResult::valid($data);
    }
}
