<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use InvalidArgumentException;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use Webmozart\Assert\Assert;

use function is_callable;

/**
 * @template A
 * @template E
 * @template B
 * @implements Validation<A, E, B>
 */
final class All implements Validation
{
    /** @var Validation<A, E, B>[] */
    private $validations;

    /**
     * @var callable(E[], E[]): E[]
     */
    private $errorFormatter;

    /**
     * @param Validation<A, E, B>[] $validations
     * @param null|callable(E[], E[]): E[] $errorFormatter
     * @throws InvalidArgumentException
     */
    private function __construct(array $validations, ?callable $errorFormatter = null)
    {
        Assert::allIsInstanceOf($validations, Validation::class);

        $this->validations = $validations;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            'array_merge';
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
     * @param callable(F[], F[]): F[] $errorFormatter
     * @return self<C, F, D>
     * @throws InvalidArgumentException
     */
    public static function validationsWithFormatter(array $validations, callable $errorFormatter)
    {
        return new self($validations, $errorFormatter);
    }

    /**
     * @param A $data
     * @param array $context
     * @return ValidationResult<E, B>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        /** @var ValidationResult<E, B> $result */
        $result = ValidationResult::valid($data);

        foreach ($this->validations as $validation) {
            $result = $result->join(
                $validation->validate($data, $context),
                /**
                 * @param B $a
                 * @param B $b
                 * @return B
                 */
                function ($a, $b) {
                    return $a;
                },
                $this->errorFormatter
            );
        }

        return $result;
    }
}
