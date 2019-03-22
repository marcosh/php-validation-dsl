<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class IsInteger extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-integer.not-an-integer';

    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion('is_int', $data, $context);
    }
}
