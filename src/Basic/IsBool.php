<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function is_bool;
use function is_callable;

final class IsBool implements Validation
{
    public const NOT_A_BOOL = 'is-bool.not-a-bool';

    /**
     * @var callable $data -> string[]
     */
    private $errorFormatter;

    public function __construct(?callable $errorFormatter = null)
    {
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            function ($data) {
                return [self::NOT_A_BOOL];
            };
    }

    public static function withFormatter(callable $errorFormatter): self
    {
        return new self($errorFormatter);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        if (! is_bool($data)) {
            return ValidationResult::errors(($this->errorFormatter)($data));
        }

        return ValidationResult::valid($data);
    }
}
