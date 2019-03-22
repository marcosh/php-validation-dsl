<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class IsResource extends ComposingAssertion implements Validation
{
    public const MESSAGE = 'is-resource.not-a-resource';

    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validateAssertion('is_resource', $data, $context);
    }
}
