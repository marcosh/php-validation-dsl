<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL;

use Marcosh\PhpValidationDSL\Result\ValidationResult;

interface Validation
{
    public function validate($data): ValidationResult;
}
