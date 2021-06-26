<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

/**
 * @template A
 * @template E
 * @template B
 * @template C
 * @implements Validation<A, E, C>
 */
final class Map implements Validation
{
    /** @var Validation<A, E, B> */
    private $validation;

    /** @var callable(B): C */
    private $function;

    /**
     * @param Validation<A, E, B> $validation
     * @param callable(B): C $function
     */
    private function __construct(
        Validation $validation,
        callable $function
    ) {
        $this->validation = $validation;
        $this->function = $function;
    }

    /**
     * @template D
     * @template H
     * @template F
     * @template G
     * @param Validation<D, H, F> $validation
     * @param callable(F): G $function
     * @return self<D, H, F, G>
     */
    public static function to(
        Validation $validation,
        callable $function
    ): self {
        return new self($validation, $function);
    }

    /**
     * @param A $data
     * @param array $context
     * @return ValidationResult<E, C>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validation->validate($data, $context)->map($this->function);
    }
}
