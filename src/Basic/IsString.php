<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class IsString extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-string.not-a-string';

    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion('is_string', $data, $context);
    }
}
