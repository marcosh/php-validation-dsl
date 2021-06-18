<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL;

use Marcosh\PhpValidationDSL\Result\ValidationResult;

/**
 * @template A
 * @template E
 * @template B
 */
interface Validation
{
    /**
     * @param A $data
     * @param array $context
     * @return ValidationResult<E, B>
     */
    public function validate($data, array $context = []): ValidationResult;
}
