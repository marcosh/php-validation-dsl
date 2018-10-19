<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function is_int;

final class IsInteger implements Validation
{
    public const NOT_AN_INTEGER = 'is-integer.not-an-integer';

    public function validate($data, array $context = []): ValidationResult
    {
        if (! is_int($data)) {
            return ValidationResult::errors([self::NOT_AN_INTEGER]);
        }

        return ValidationResult::valid($data);
    }
}
