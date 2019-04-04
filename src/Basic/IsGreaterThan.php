<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Basic\IsAsAsserted;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;

final class IsGreaterThan extends Bound implements Validation
{
    public const MESSAGE = 'is-greater-than.not-greater-than';

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
             * @psalm-param T $bound
             * @psalm-param T $data
             */
            function ($bound, $data): bool {
                return $data > $bound;
            },
            $data,
            $context
        );
    }
}
