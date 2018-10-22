<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use function is_iterable;

final class IsIterable
{
    public const NOT_AN_ITERABLE = 'is-iterable.not-an-iterable';

    public function validate($data, array $context = []): ValidationResult
    {
        if (! is_iterable($data)) {
            return ValidationResult::errors([self::NOT_AN_ITERABLE]);
        }

        return ValidationResult::valid($data);
    }
}
