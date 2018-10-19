<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function is_float;

final class IsFloat implements Validation
{
    public const NOT_A_FLOAT = 'is-float.not-a-float';

    public function validate($data, array $context = []): ValidationResult
    {
        if (! is_float($data)) {
            return ValidationResult::errors([self::NOT_A_FLOAT]);
        }

        return ValidationResult::valid($data);
    }
}
