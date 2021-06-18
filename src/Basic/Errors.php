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
final class Errors extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'errors.invalid-data';

    /**
     * @param A $data
     * @return ValidationResult<A>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        $alwaysFalse =
            /**
             * @param A $data
             * @return false
             */
            function ($data): bool {
                return false;
            };

        return $this->validateAssertion($alwaysFalse, $data, $context);
    }
}
