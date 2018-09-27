<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function is_string;

final class IsString implements Validation
{
    public const NOT_A_STRING = 'is-string.not-a-string';

    public function validate($data): ValidationResult
    {
        if (! is_string($data)) {
            return ValidationResult::errors([self::NOT_A_STRING]);
        }

        return ValidationResult::valid($data);
    }
}
