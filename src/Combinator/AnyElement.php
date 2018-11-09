<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

final class AnyElement implements Validation
{
    /**
     * @var Validation
     */
    private $elementValidation;

    /**
     * @var callable : $key -> $resultMessages -> $validationMessages -> array
     */
    private $errorFormatter;

    private function __construct(Validation $validation, ?callable $errorFormatter = null)
    {
        $this->elementValidation = $validation;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            function ($key, $resultMessages, $validationMessages) {
                $resultMessages[$key] = $validationMessages;

                return $resultMessages;
            };
    }

    public static function validation(Validation $validation)
    {
        return new self($validation);
    }

    public static function validationWithFormatter(Validation $validation, callable  $errorFormatter)
    {
        return new self($validation, $errorFormatter);
    }

    /**
     * @param array $data
     * @param array $context
     * @return ValidationResult
     */
    public function validate($data, array $context = []): ValidationResult
    {
        $errorFormatter = $this->errorFormatter;

        $result = ValidationResult::errors([]);

        foreach ($data as $key => $element) {
            $result = $result->meet(
                $this->elementValidation->validate($data[$key], $context),
                function ($resultMessages, $validationMessages) use ($key, $errorFormatter) {
                    return $errorFormatter($key, $resultMessages, $validationMessages);
                }
            );
        }

        return $result->map(function () use ($data) {
            return $data;
        });
    }
}
