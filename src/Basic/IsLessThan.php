<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;

/**
 * @template E
 * @template A
 * @extends Compare<E, A>
 * @implements Validation<A, E, A>
 */
final class IsLessThan extends Compare implements Validation
{
    public const MESSAGE = 'is-less-than.not-less-than';

    /**
     * @param A $data
     * @return ValidationResult<E, A>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion(
            /**
             * @param A $bound
             * @param A $data
             */
            function ($bound, $data): bool {
                return $data < $bound;
            },
            $data,
            $context
        );
    }
}
