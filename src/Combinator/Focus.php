<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class Focus implements Validation
{
    /**
     * @var callable
     */
    private $focus;

    /**
     * @var Validation
     */
    private $validation;

    /**
     * Focus constructor.
     * @param callable $focus :: $data -> mixed
     * @param Validation $validation
     */
    private function __construct(callable $focus, Validation $validation)
    {
        $this->focus = $focus;
        $this->validation = $validation;
    }

    /**
     * @param callable $focus :: $data -> mixed
     * @param Validation $validation
     * @return self
     */
    public static function on(callable $focus, Validation $validation): self
    {
        return new self($focus, $validation);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validation->validate(($this->focus)($data), $context);
    }
}
