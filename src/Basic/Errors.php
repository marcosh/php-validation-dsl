<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class Errors extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'errors.invalid-data';

    public function validate($data, array $context = []): ValidationResult
    {
        $alwaysFalse = function ($data) {
            return false;
        };

        return parent::validateAssertion($alwaysFalse, $data, $context);
    }
}
