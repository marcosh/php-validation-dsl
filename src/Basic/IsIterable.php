<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;
use function is_iterable;

final class IsIterable implements Validation
{
    public const NOT_AN_ITERABLE = 'is-iterable.not-an-iterable';

    /**
     * @var callable $data -> string[]
     */
    private $errorFormatter;

    public function __construct(?callable $errorFormatter = null)
    {
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            function ($data) {
                return [self::NOT_AN_ITERABLE];
            };
    }

    public static function withFormatter(callable $errorFormatter): self
    {
        return new self($errorFormatter);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        if (! is_iterable($data)) {
            return ValidationResult::errors(($this->errorFormatter)($data));
        }

        return ValidationResult::valid($data);
    }
}
