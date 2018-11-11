<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;
use function preg_match;

final class Regex implements Validation
{
    public const MATCH_FAILED = 'regex.match-failed';

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var callable $key -> $data -> string[]
     */
    private $errorFormatter;

    private function __construct(string $pattern, ?callable $errorFormatter = null)
    {
        $this->pattern = $pattern;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            function (string $key, $data) {
                return [self::MATCH_FAILED];
            };
    }

    public static function withPattern(string $pattern):self
    {
        return new self($pattern);
    }

    public static function withPatternAndFormatter(string $pattern, callable $errorFormatter):self
    {
        return new self($pattern, $errorFormatter);
    }

    public static function withPatternAndTranslator(string $pattern, Translator $translator): self
    {
        return new self(
            $pattern,
            function (string $key, $data) use ($translator) {
                return [$translator->translate(self::MATCH_FAILED)];
            }
        );
    }

    public function validate($data, array $context = []): ValidationResult
    {
        $match = preg_match($this->pattern, $data);

        if (false === $match || 0 === $match) {
            return ValidationResult::errors(($this->errorFormatter)($this->pattern, $data));
        }

        return ValidationResult::valid($data);
    }
}
