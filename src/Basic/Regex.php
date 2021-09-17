<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;

use function is_callable;
use function preg_match;

/**
 * @template E
 * @implements Validation<string, E, string>
 */
final class Regex implements Validation
{
    public const MESSAGE = 'regex.match-failed';

    /** @var string */
    private $pattern;

    /** @var callable(string, string): E[] */
    private $errorFormatter;

    /**
     * @param null|callable(string, string): E[] $errorFormatter
     */
    private function __construct(string $pattern, ?callable $errorFormatter = null)
    {
        $this->pattern = $pattern;

        /** @psalm-suppress PossiblyInvalidPropertyAssignmentValue */
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            /**
             * @return string[]
             */
            function (string $pattern, string $data): array {
                return [self::MESSAGE];
            };
    }

    /**
     * @return self<string>
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public static function withPattern(string $pattern): self
    {
        return new self($pattern);
    }

    /**
     * @template F
     * @param callable(string, string): F[] $errorFormatter
     * @return self<F>
     */
    public static function withPatternAndFormatter(string $pattern, callable $errorFormatter): self
    {
        return new self($pattern, $errorFormatter);
    }

    /**
     * @return self<string>
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public static function withPatternAndTranslator(string $pattern, Translator $translator): self
    {
        return new self(
            $pattern,
            /**
             * @return string[]
             */
            function (string $pattern, string $data) use ($translator): array {
                return [$translator->translate(self::MESSAGE)];
            }
        );
    }

    /**
     * @param string $data
     * @return ValidationResult<E, string>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        $match = preg_match($this->pattern, $data);

        if (false === $match || 0 === $match) {
            /** @var ValidationResult<E, string> $ret */
            $ret = ValidationResult::errors(($this->errorFormatter)($this->pattern, $data));

            return $ret;
        }

        return ValidationResult::valid($data);
    }
}
