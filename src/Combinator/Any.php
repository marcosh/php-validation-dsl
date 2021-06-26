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
 * @template B
 * @implements Validation<A, E[], B>
 */
final class Any implements Validation
{
    public const NOT_EVEN_ONE = 'any.not-even-one';

    /** @var Validation<A, E, B>[] */
    private $validations;

    /** @var callable(E[]): E[][] */
    private $errorFormatter;

    /**
     * @param Validation<A, E, B>[] $validations
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
     * @template D
     * @param Validation<C, F, D>[] $validations
     * @return self<C, F, D>
     * @throws InvalidArgumentException
     */
    public static function validations(array $validations): self
    {
        return new self($validations);
    }

    /**
     * @template C
     * @template F
     * @template D
     * @param Validation<C, F, D>[] $validations
     * @param callable(F[]): F[][] $errorFormatter
     * @return self<C, F, D>
     * @throws InvalidArgumentException
     */
    public static function validationsWithFormatter(array $validations, callable $errorFormatter): self
    {
        return new self($validations, $errorFormatter);
    }

    /**
     * @template C
     * @template F
     * @template D
     * @param Validation<C, F, D>[] $validations
     * @param Translator $translator
     * @return self<C, F, D>
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
     * @return ValidationResult<E[], B>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        /** @var ValidationResult<E, B> $result */
        $result = ValidationResult::errors([]);

        foreach ($this->validations as $validation) {
            $result = $result->meet(
                $validation->validate($data, $context)->mapErrors(function ($e) {return [$e];}),
                /**
                 * @param B $x
                 * @param B $y
                 * @return B
                 */
                function ($x, $y) {
                    return $x;
                },
                'array_merge'
            );
        }

        return $result->mapErrors($this->errorFormatter);
    }
}
