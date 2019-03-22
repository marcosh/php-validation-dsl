<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

final class EveryElement implements Validation
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

    public static function validation(Validation $validation): self
    {
        return new self($validation);
    }

    public static function validationWithFormatter(Validation $validation, callable  $errorFormatter): self
    {
        return new self($validation, $errorFormatter);
    }

    /**
     * @param mixed $data should receive an array; the type hint is mixed because of contravariance
     * @param array $context
     * @return ValidationResult
     */
    public function validate($data, array $context = []): ValidationResult
    {
        $errorFormatter = $this->errorFormatter;

        $result = ValidationResult::valid($data);

        foreach ($data as $key => $element) {
            $result = $result->join(
                $this->elementValidation->validate($data[$key], $context),
                function ($a, $b) {
                    return $a;
                },
                function ($resultMessages, $validationMessages) use ($key, $errorFormatter) {
                    return $errorFormatter($key, $resultMessages, $validationMessages);
                }
            );
        }

        return $result;
    }
}
