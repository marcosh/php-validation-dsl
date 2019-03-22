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

    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion(
            function ($bound, $data) {
                return $data > $bound;
            },
            $data,
            $context
        );
    }
}
