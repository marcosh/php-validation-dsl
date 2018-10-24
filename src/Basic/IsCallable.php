<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use function is_callable;

final class IsCallable
{
    public const NOT_A_CALLABLE = 'is-callable.not-a-callable';

    public function validate($data, array $context = []): ValidationResult
    {
        if (! is_callable($data)) {
            return ValidationResult::errors([self::NOT_A_CALLABLE]);
        }

        return ValidationResult::valid($data);
    }
}
