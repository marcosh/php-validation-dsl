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
final class IsString extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-string.not-a-string';

    /**
     * @param A $data
     * @return ValidationResult<E, A>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion('is_string', $data, $context);
    }
}
