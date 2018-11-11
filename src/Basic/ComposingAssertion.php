<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;

abstract class ComposingAssertion
{
    /**
     * @var IsAsAsserted
     */
    protected $isAsAsserted;

    abstract public function __construct(?callable $errorFormatter = null);

    public static function withFormatter(callable $errorFormatter): self
    {
        return new static($errorFormatter);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        return $this->isAsAsserted->validate($data, $context);
    }
}
