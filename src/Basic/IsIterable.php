<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class IsIterable extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-iterable.not-an-iterable';

    public function validate($data, array $context = []): ValidationResult
    {
        return parent::validateAssertion('is_iterable', $data, $context);
    }
}
