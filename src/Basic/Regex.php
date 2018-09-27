<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use function preg_match;

final class Regex
{
    private const MATCH_FAILED = 'regex.match-failed';

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

    public function validate($data): ValidationResult
    {
        $match = preg_match($this->pattern, $data);

        if (false === $match || 0 === $match) {
            return ValidationResult::errors([self::MATCH_FAILED]);
        }

        return ValidationResult::valid($data);
    }
}
