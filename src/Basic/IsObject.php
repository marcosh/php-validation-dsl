<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;
use function is_object;

final class IsObject implements Validation
{
    public const NOT_AN_OBJECT = 'is-object.not-an-object';

    /**
     * @var callable $data -> string[]
     */
    private $errorFormatter;

    public function __construct(?callable $errorFormatter = null)
    {
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            function ($data) {
                return [self::NOT_AN_OBJECT];
            };
    }

    public static function withFormatter(callable $errorFormatter): self
    {
        return new self($errorFormatter);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        if (! is_object($data)) {
            return ValidationResult::errors(($this->errorFormatter)($data));
        }

        return ValidationResult::valid($data);
    }
}
