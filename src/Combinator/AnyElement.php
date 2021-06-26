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
final class AnyElement implements Validation
{
    /** @var Validation<A, E, B> */
    private $elementValidation;

    /** @var callable(E[][], E[][]): E[][] */
    private $errorFormatter;

    /**
     * @param Validation<A, E, B> $validation
     * @param null|callable(E[][], E[][]): E[][] $errorFormatter
     */
    private function __construct(Validation $validation, ?callable $errorFormatter = null)
    {
        $this->elementValidation = $validation;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            /**
             * @param E[][] $resultMessages
             * @param E[][] $validationMessages
             * @return E[][]
             */
            function (array $resultMessages, array $validationMessages): array {
                return array_merge($resultMessages, $validationMessages);
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
     * @param callable(F[][], F[][]): F[][] $errorFormatter
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
        $errorFormatter = $this->errorFormatter;

        /** @var ValidationResult<E[], B[]> $result */
        $result = ValidationResult::errors([]);

        foreach ($data as $key => $element) {
            /** @var ValidationResult<E[], B[]> $newResult */
            $newResult = $this->elementValidation->validate($element, $context)
                ->map(function ($v) use ($key) {return [$key => $v];})
                ->mapErrors(function ($messages) use ($key) {return [$key => $messages];});

            $result = $result->meet(
                $newResult,
                /**
                 * @param B[] $result
                 * @param B[] $next
                 * @return B[]
                 */
                function (array $result, array $next)
                {
                    return array_merge($result, $next);
                },
                /**
                 * @param E[][] $resultMessages
                 * @param E[][] $validationMessages
                 * @return E[][]
                 */
                function (array $resultMessages, array $validationMessages) use ($key, $errorFormatter) {
                    return $errorFormatter($resultMessages, $validationMessages);
                }
            );
        }

        return $result;
    }
}
