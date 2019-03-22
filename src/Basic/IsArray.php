<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class IsArray extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-array.no-an-array';

    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion('is_array', $data, $context);
    }
}
