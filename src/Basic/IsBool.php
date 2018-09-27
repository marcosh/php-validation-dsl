<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function is_bool;

final class IsBool implements Validation
{
    public const NOT_A_BOOL = 'is-bool.not-a-bool';

    public function validate($data): ValidationResult
    {
        if (! is_bool($data)) {
            return ValidationResult::errors([self::NOT_A_BOOL]);
        }

        return ValidationResult::valid($data);
    }
}
