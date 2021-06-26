<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

/**
 * @template A
 * @template E
 * @template B
 * @implements Validation<A[], E[], B[]>
 */
final class EveryElement implements Validation
{
    /** @var Validation<A, E, B> */
    private $elementValidation;

    /** @var callable callable(array-key, E[][], E[]): E[][] */
    private $errorFormatter;

    /**
     * @param Validation<A, E, B> $validation
     * @param null|callable(array-key, E[][], E[]): E[][] $errorFormatter
     */
    private function __construct(Validation $validation, ?callable $errorFormatter = null)
    {
        $this->elementValidation = $validation;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            /**
             * @param array-key $key
             * @param E[][] $resultMessages
             * @param E[] $validationMessages
             * @return E[]
             */
            function ($key, array $resultMessages, array $validationMessages): array {
                $resultMessages[$key] = $validationMessages;

                return $resultMessages;
            };
    }

    /**
     * @template C
     * @template F
     * @template D
     * @param Validation<C, F, D> $validation
     * @return self<C, F, D>
     */
    public static function validation(Validation $validation): self
    {
        return new self($validation);
    }

    /**
     * @template C
     * @template F
     * @template D
     * @param Validation<C, F, D> $validation
     * @param callable(array-key, F[][], F[]): F[][] $errorFormatter
     * @return self<C, F, D>
     */
    public static function validationWithFormatter(Validation $validation, callable  $errorFormatter): self
    {
        return new self($validation, $errorFormatter);
    }

    /**
     * @param A[] $data
     * @param array $context
     * @return ValidationResult<E[], B[]>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        /** @var callable(array-key, E[][], E[]): E[][] $errorFormatter */
        $errorFormatter = $this->errorFormatter;

        /** @var ValidationResult<E[], B[]> $result */
        $result = ValidationResult::valid($data);

        foreach ($data as $key => $element) {
            $result = $result->join(
                $this->elementValidation->validate($element, $context),
                /**
                 * @psalm-param B[] $result
                 * @psalm-param B $next
                 * @return B[]
                 */
                function (array $result, $next) use ($key) {
                    return array_merge($result, [$key => $next]);
                },
                /**
                 * @param E[][] $resultMessages
                 * @param E[] $validationMessages
                 * @return E[][]
                 */
                function (array $resultMessages, array $validationMessages) use ($key, $errorFormatter): array {
                    return $errorFormatter($key, $resultMessages, $validationMessages);
                }
            );
        }

        return $result;
    }
}
