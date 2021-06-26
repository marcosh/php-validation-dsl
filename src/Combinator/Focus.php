<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

/**
 * @template A
 * @template B
 * @template E
 * @implements Validation<A, E, A>
 */
final class Focus implements Validation
{
    /** @var callable(A): B */
    private $focus;

    /** @var Validation<B, E, mixed> */
    private $validation;

    /**
     * @param callable(A): B $focus
     * @param Validation<B, E, mixed> $validation
     */
    private function __construct(callable $focus, Validation $validation)
    {
        $this->focus = $focus;
        $this->validation = $validation;
    }

    /**
     * @template C
     * @template D
     * @template F
     * @param callable(C): D $focus
     * @param Validation<D, F, mixed> $validation
     * @return self<C, D, F>
     */
    public static function on(callable $focus, Validation $validation): self
    {
        return new self($focus, $validation);
    }

    /**
     * @param A $data
     * @param array $context
     * @return ValidationResult<E, A>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validation->validate(($this->focus)($data), $context)
            // would really need a Lens here to update the outer value applying the callable to the inner value
            ->map(
                /**
                 * @return A
                 */
                function () use ($data) {
                    return $data;
                }
            );
    }
}
