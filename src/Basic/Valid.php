<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

/**
 * @template E
 * @template A
 * @implements Validation<A, E, A>
 */
final class Valid implements Validation
{
    /**
     * @param A $data
     * @return ValidationResult<E, A>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        return ValidationResult::valid($data);
    }
}
