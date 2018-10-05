<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;

final class IsNotNull
{
    public const NOT_NOT_NULL = 'is-not-null.not-not-null';

    public function validate($data, array $context = []): ValidationResult
    {
        if ($data === null) {
            return ValidationResult::errors([self::NOT_NOT_NULL]);
        }

        return ValidationResult::valid($data);
    }
}
