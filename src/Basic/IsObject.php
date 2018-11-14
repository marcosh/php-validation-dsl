<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class IsObject extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-object.not-an-object';

    public function validate($data, array $context = []): ValidationResult
    {
        return parent::validateAssertion('is_object', $data, $context);
    }
}
