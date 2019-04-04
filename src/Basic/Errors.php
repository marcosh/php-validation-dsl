<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class Errors extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'errors.invalid-data';

    /**
     * @template T
     * @psalm-param T $data
     * @param mixed $data
     * @param array $context
     * @return ValidationResult
     */
    public function validate($data, array $context = []): ValidationResult
    {
        $alwaysFalse =
            /**
             * @param mixed $data
             * @psalm-param T $data
             * @return false
             */
            function ($data): bool {
                return false;
            };

        return $this->validateAssertion($alwaysFalse, $data, $context);
    }
}
