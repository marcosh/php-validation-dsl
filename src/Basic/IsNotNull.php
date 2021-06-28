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
final class IsNotNull extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-not-null.not-not-null';

    /**
     * @param A $data
     * @return ValidationResult<E, A>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion(
            /**
             * @param A $data
             */
            function ($data): bool {
                return null !== $data;
            },
            $data,
            $context
        );
    }
}
