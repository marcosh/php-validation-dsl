<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL;

use Marcosh\PhpValidationDSL\Result\ValidationResult;

interface Validation
{
    /**
     * @param mixed $data
     * @param array $context
     * @return ValidationResult
     */
    public function validate($data, array $context = []): ValidationResult;
}
