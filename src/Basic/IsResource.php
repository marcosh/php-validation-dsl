<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use function is_resource;

final class IsResource
{
    public const NOT_A_RESOURCE = 'is-resource.not-a-resource';

    public function validate($data, array $context = []): ValidationResult
    {
        if (! is_resource($data)) {
            return ValidationResult::errors([self::NOT_A_RESOURCE]);
        }

        return ValidationResult::valid($data);
    }
}
