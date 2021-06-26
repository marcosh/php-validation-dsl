<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

/**
 * @template A
 * @template E
 * @template F
 * @template B
 * @implements Validation<A, F, B>
 */
final class MapErrors implements Validation
{
    /** @var Validation<A, E, B> */
    private $validation;

    /**
     * @var callable(E[]): F[]
     */
    private $function;

    /**
     * @param Validation<A, E, B> $validation
     * @param callable(E[]): F[] $function
     */
    private function __construct(
        Validation $validation,
        callable $function
    ) {
        $this->validation = $validation;
        $this->function = $function;
    }

    /**
     * @template C
     * @template G
     * @template H
     * @template D
     * @param Validation<C, G, D> $validation
     * @param callable(G[]): H[] $function
     * @return self<C, G, H, D>
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
     * @return ValidationResult<F, B>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validation->validate($data, $context)->mapErrors($this->function);
    }
}
