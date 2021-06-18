<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use function is_callable;

/**
 * @template A
 */
abstract class ComposingAssertion
{
    public const MESSAGE = 'composing-assertion.not-as-asserted';

    /** @var null|callable(A): string[] */
    private $errorFormatter;

    /**
     * @param null|callable(A): string[] $errorFormatter
     */
    public function __construct(?callable $errorFormatter = null)
    {
        $this->errorFormatter = $errorFormatter;
    }

    /**
     * @template B
     * @param callable(B): string[] $errorFormatter
     * @return self<B>
     */
    public static function withFormatter(callable $errorFormatter): self
    {
        /** @psalm-suppress UnsafeInstantiation */
        return new static($errorFormatter);
    }

    public static function withTranslator(Translator $translator): self
    {
        /** @psalm-suppress UnsafeInstantiation */
        return new static(
            /**
             * @template T
             * @param T $data
             * @return string[]
             */
            function ($data) use ($translator): array {
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
     * @param callable(A): bool $assertion
     * @param A $data
     * @return ValidationResult<A>
     */
    protected function validateAssertion(callable $assertion, $data, array $context = []): ValidationResult
    {
        return IsAsAsserted::withAssertionAndErrorFormatter(
            $assertion,
            is_callable($this->errorFormatter) ?
                $this->errorFormatter :
                /**
                 * @param A $data
                 * @return string[]
                 */
                function ($data): array {
                    /** @var string $message */
                    $message = static::MESSAGE;

                    return [$message];
                }
        )->validate($data, $context);
    }
}
