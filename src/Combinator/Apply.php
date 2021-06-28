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
final class Apply implements Validation
{
    /** @var Validation<A, E, B> */
    private $validation;

    /**
     * @var ValidationResult<E, callable(B): C>
     */
    private $validationFunction;

    /**
     * @param Validation<A, E, B> $validation
     * @param ValidationResult<E, callable(B): C> $validationFunction
     */
    private function __construct(Validation $validation, ValidationResult $validationFunction)
    {
        $this->validation = $validation;
        $this->validationFunction = $validationFunction;
    }

    /**
     * @template D
     * @template H
     * @template F
     * @template G
     * @param Validation<D, H, F> $validation
     * @param ValidationResult<H, callable(F): G> $validationFunction
     * @return self<D, H, F, G>
     */
    public static function to(Validation $validation, ValidationResult $validationFunction): self
    {
        return new self($validation, $validationFunction);
    }

    /**
     * @param A $data
     * @param array $context
     * @return ValidationResult<E, C>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validation->validate($data, $context)->apply($this->validationFunction);
    }
}
