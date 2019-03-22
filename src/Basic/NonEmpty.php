<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class NonEmpty extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'non-empty.empty';

    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion(
            function ($data) {
                return !empty($data);
            },
            $data,
            $context
        );
    }
}
