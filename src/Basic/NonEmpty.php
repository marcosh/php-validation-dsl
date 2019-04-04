<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class NonEmpty extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'non-empty.empty';

    /**
     * @template T
     * @psalm-param T $data
     * @param mixed $data
     * @param array $context
     * @return ValidationResult
     */
    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion(
            /**
             * @psalm-param T $data
             */
            function ($data): bool {
                return !empty($data);
            },
            $data,
            $context
        );
    }
}
