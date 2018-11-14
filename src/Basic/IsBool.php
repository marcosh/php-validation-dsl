<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class IsBool extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-bool.not-a-bool';

    public function validate($data, array $context = []): ValidationResult
    {
        return parent::validateAssertion('is_bool', $data, $context);
    }
}
