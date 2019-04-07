<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;

final class IsLessThan extends Compare implements Validation
{
    public const MESSAGE = 'is-less-than.not-less-than';

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
                return $data < $bound;
            },
            $data,
            $context
        );
    }
}
