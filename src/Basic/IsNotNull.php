<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class IsNotNull extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-not-null.not-not-null';

    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion(
            function ($data) {
                return null !== $data;
            },
            $data,
            $context
        );
    }
}
