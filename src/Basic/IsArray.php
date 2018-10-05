<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function is_array;

final class IsArray implements Validation
{
    public const NOT_AN_ARRAY = 'is-array.no-an-array';

    public function validate($data, array $context = []): ValidationResult
    {
        if (! is_array($data)) {
            return ValidationResult::errors([self::NOT_AN_ARRAY]);
        }

        return ValidationResult::valid($data);
    }
}
