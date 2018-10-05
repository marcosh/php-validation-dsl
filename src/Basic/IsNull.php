<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class IsNull implements Validation
{
    public const NOT_NULL = 'is-null.not-null';

    public function validate($data, array $context = []): ValidationResult
    {
        if ($data !== null) {
            return ValidationResult::errors([self::NOT_NULL]);
        }

        return ValidationResult::valid($data);
    }
}
