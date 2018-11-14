<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class IsNull extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-null.not-null';

    public function validate($data, array $context = []): ValidationResult
    {
        return parent::validateAssertion(
            function ($data) {
                return null === $data;
            },
            $data,
            $context
        );
    }
}
