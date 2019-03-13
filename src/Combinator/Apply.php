<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class Apply implements Validation
{
    /**
     * @var Validation
     */
    private $validation;

    /**
     * @var ValidationResult wrapping a callable
     */
    private $validationFunction;

    private function __construct(Validation $validation, ValidationResult $validationFunction)
    {
        $this->validation = $validation;
        $this->validationFunction = $validationFunction;
    }

    public static function to(Validation $validation, ValidationResult $validationFunction): self
    {
        return new self($validation, $validationFunction);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validation->validate($data, $context)->apply($this->validationFunction);
    }
}
