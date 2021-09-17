<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

use function is_callable;

/**
 * @template A
 * @template E
 * @implements Validation<A[], E[], A[]>
 */
final class AnyElement implements Validation
{
    /** @var Validation<A, E, A> */
    private $elementValidation;

    /** @var callable(array-key, E[][], E[]): E[][] */
    private $errorFormatter;

    /**
     * @param Validation<A, E, A> $validation
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
             * @return E[][]
             */
            function ($key, array $resultMessages, array $validationMessages): array {
                $resultMessages[$key] = $validationMessages;

                return $resultMessages;
            };
    }

    /**
     * @template C
     * @template F
     * @param Validation<C, F, C> $validation
     * @return self<C, F>
     */
    public static function validation(Validation $validation): self
    {
        return new self($validation);
    }

    /**
     * @template C
     * @template F
     * @param Validation<C, F, C> $validation
     * @param callable(array-key, F[][], F[]): F[][] $errorFormatter
     * @return self<C, F>
     */
    public static function validationWithFormatter(Validation $validation, callable $errorFormatter): self
    {
        return new self($validation, $errorFormatter);
    }

    /**
     * @param A[] $data
     * @param array $context
     * @return ValidationResult<E[], A[]>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        $errorFormatter = $this->errorFormatter;

        /** @var ValidationResult<E[], A[]> $result */
        $result = ValidationResult::errors([]);

        foreach ($data as $key => $element) {
            $result = $result->meet(
                $this->elementValidation->validate($element, $context),
                /**
                 * @param A[] $x
                 * @param A $y
                 * @return A[]
                 */
                function (array $x, $y) {
                    return $x;
                },
                /**
                 * @param A[] $x
                 * @return A[]
                 */
                function (array $x) {
                    return $x;
                },
                /**
                 * @param A $x
                 * @return A[]
                 */
                function ($x) {
                    return [$x];
                },
                /**
                 * @param E[][] $resultMessages
                 * @param E[] $validationMessages
                 * @return E[][]
                 */
                function (array $resultMessages, array $validationMessages) use ($key, $errorFormatter) {
                    return $errorFormatter($key, $resultMessages, $validationMessages);
                }
            );
        }

        return $result->map(
            function () use ($data) {
                return $data;
            }
        );
    }
}
