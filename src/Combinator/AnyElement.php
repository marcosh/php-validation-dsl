<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class AnyElement implements Validation
{
    /**
     * @var Validation
     */
    private $elementValidation;

    private function __construct(Validation $validation)
    {
        $this->elementValidation = $validation;
    }

    public static function validation(Validation $validation)
    {
        return new self($validation);
    }

    /**
     * @param array $data
     * @param array $context
     * @return ValidationResult
     */
    public function validate($data, array $context = []): ValidationResult
    {
        $result = ValidationResult::errors([]);

        foreach ($data as $key => $element) {
            $result = $result->meet(
                $this->elementValidation->validate($data[$key], $context),
                function ($resultMessages, $validationMessages) use ($key) {
                    $resultMessages[$key] = $validationMessages;

                    return $resultMessages;
                }
            );
        }

        return $result->map(function () use ($data) {
            return $data;
        });
    }
}
