<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

/**
 * @template E
 * @template A
 * @extends ComposingAssertion<E, A>
 * @implements Validation<A, E, A>
 */
final class IsFloat extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-float.not-a-float';

    /**
     * @param A $data
     * @return ValidationResult<E, A>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion('is_float', $data, $context);
    }
}
