<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use function is_object;

final class IsObject
{
    public const NOT_AN_OBJECT = 'is-object.not-an-object';

    public function validate($data, array $context = []): ValidationResult
    {
        if (! is_object($data)) {
            return ValidationResult::errors([self::NOT_AN_OBJECT]);
        }

        return ValidationResult::valid($data);
    }
}
