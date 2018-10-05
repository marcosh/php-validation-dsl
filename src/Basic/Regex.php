<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function preg_match;

final class Regex implements Validation
{
    public const MATCH_FAILED = 'regex.match-failed';

    /**
     * @var string
     */
    private $pattern;

    private function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public static function withPattern(string $pattern):self
    {
        return new self($pattern);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        $match = preg_match($this->pattern, $data);

        if (false === $match || 0 === $match) {
            return ValidationResult::errors([self::MATCH_FAILED]);
        }

        return ValidationResult::valid($data);
    }
}