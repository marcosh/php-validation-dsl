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
     * @var callable with signature $key -> $resultMessages -> $validationMessages -> array
     */
    private $errorFormatter;

    private function __construct(Validation $validation, ?callable $errorFormatter = null)
    {
        $this->elementValidation = $validation;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            /**
             * @template K
             * @template V
             * @psalm-param K $key
             * @param array<K, V> $resultMessages
             * @param array $validationMessages
             * @return array<K, V>
             */
            function ($key, array $resultMessages, array $validationMessages): array {
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
                /**
                 * @template T
                 * @template U
                 * @psalm-param T $a
                 * @psalm-param U $b
                 * @return T
                 */
                function ($a, $b) {
                    return $a;
                },
                function (array $resultMessages, array $validationMessages) use ($key, $errorFormatter): array {
                    return $errorFormatter($key, $resultMessages, $validationMessages);
                }
            );
        }

        return $result;
    }
}
