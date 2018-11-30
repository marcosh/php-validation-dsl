<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;

final class Map implements Validation
{
    /**
     * @var Validation
     */
    private $validation;

    /**
     * @var callable
     */
    private $function;

    private function __construct(
        Validation $validation,
        callable $function
    ) {
        $this->validation = $validation;
        $this->function = $function;
    }

    public static function to(
        Validation $validation,
        callable $function
    ) {
        return new self($validation, $function);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        return $this->validation->validate($data, $context)->map($this->function);
    }
}
