<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class Valid implements Validation
{
    public function validate($data, array $context = []): ValidationResult
    {
        return ValidationResult::valid($data);
    }
}
