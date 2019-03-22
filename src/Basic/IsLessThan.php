<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;

final class IsLessThan extends Bound implements Validation
{
    public const MESSAGE = 'is-less-than.not-less-than';

    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion(
            function ($bound, $data) {
                return $data < $bound;
            },
            $data,
            $context
        );
    }
}
