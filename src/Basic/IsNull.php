<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

/**
 * @template A
 * @extends ComposingAssertion<A>
 * @implements Validation<A, A>
 */
final class IsNull extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-null.not-null';

    /**
     * @param A $data
     * @return ValidationResult<A>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion(
            /**
             * @param A $data
             */
            function ($data): bool {
                return null === $data;
            },
            $data,
            $context
        );
    }
}
