<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class IsCallable extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-callable.not-a-callable';

    public function validate($data, array $context = []): ValidationResult
    {
        return parent::validateAssertion('is_callable', $data, $context);
    }
}
