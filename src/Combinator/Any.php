<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

use function is_callable;

/**
 * @template A
 * @template E
 * @implements Validation<A, E[], A>
 */
final class Any implements Validation
{
    public const NOT_EVEN_ONE = 'any.not-even-one';

    /** @var Validation<A, E, mixed>[] */
    private $validations;

    /** @var callable(E[]): E[][] */
    private $errorFormatter;

    /**
     * @param Validation<A, E, mixed>[] $validations
     * @param null|callable(E[]): E[][] $errorFormatter
     * @throws InvalidArgumentException
     */
    private function __construct(array $validations, ?callable $errorFormatter = null)
    {
        Assert::allIsInstanceOf($validations, Validation::class);

        $this->validations = $validations;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            /**
             * @param E[] $messages
             * @return E[][]
             */
            function (array $messages): array {
                return [
                    self::NOT_EVEN_ONE => $messages
                ];
            };
    }

    /**
     * @template C
     * @template F
     * @param Validation<C, F, mixed>[] $validations
     * @return self<C, F>
     * @throws InvalidArgumentException
     */
    public static function validations(array $validations): self
    {
        return new self($validations);
    }

    /**
     * @template C
     * @template F
     * @param Validation<C, F, mixed>[] $validations
     * @param callable(F[]): F[][] $errorFormatter
     * @return self<C, F>
     * @throws InvalidArgumentException
     */
    public static function validationsWithFormatter(array $validations, callable $errorFormatter): self
    {
        return new self($validations, $errorFormatter);
    }

    /**
     * @template C
     * @template F
     * @param Validation<C, F, mixed>[] $validations
     * @param Translator $translator
     * @return self<C, F>
     * @throws InvalidArgumentException
     */
    public static function validationsWithTranslator(array $validations, Translator $translator): self
    {
        return new self(
            $validations,
            /**
             * @param F[] $messages
             * @return F[][]
             */
            function (array $messages) use ($translator): array {
                return [
                    $translator->translate(self::NOT_EVEN_ONE) => $messages
                ];
            }
        );
    }

    /**
     * @param A $data
     * @param array $context
     * @return ValidationResult<E[], A>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        /** @var ValidationResult<E, A> $result */
        $result = ValidationResult::errors([]);

        foreach ($this->validations as $validation) {
            $result = $result->meet(
                $validation->validate($data, $context),
                /**
                 * @param A $x
                 * @param A $y
                 * @return A
                 */
                function ($x, $y) {
                    return $x;
                },
                /**
                 * @param A $x
                 * @return A
                 */
                function ($x) {
                    return $x;
                },
                /**
                 * @param A $x
                 * @return A
                 */
                function ($x) {
                    return $x;
                },
                'array_merge'
            );
        }

        return $result
            ->mapErrors($this->errorFormatter)
            ->map(
                function () use ($data) {
                    return $data;
                }
            );
    }
}
