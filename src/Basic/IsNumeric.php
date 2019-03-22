<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class IsNumeric extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-numeric.not-numeric';

    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion('is_numeric', $data, $context);
    }
}
