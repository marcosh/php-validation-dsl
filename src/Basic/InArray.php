<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use function in_array;
use function is_callable;

final class InArray implements Validation
{
    public const NOT_IN_ARRAY = 'in-array.not-in-array';

    /**
     * @var array
     */
    private $values;

    /**
     * @var callable with signature $values -> $data -> string[]
     */
    private $errorFormatter;

    private function __construct(array $values, ?callable $errorFormatter = null)
    {
        $this->values = $values;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            function ($values, $data) {
                return [self::NOT_IN_ARRAY];
            };
    }

    public static function withValues(array $values): self
    {
        return new self($values);
    }

    public static function withValuesAndFormatter(array $values, callable $errorFormatter): self
    {
        return new self($values, $errorFormatter);
    }

    public static function withValuesAndTranslator(array $values, Translator $translator): self
    {
        return new self(
            $values,
            function (array $values, $data) use ($translator) {
                return [$translator->translate(self::NOT_IN_ARRAY)];
            }
        );
    }

    public function validate($data, array $context = []): ValidationResult
    {
        if (! in_array($data, $this->values, true)) {
            return ValidationResult::errors(($this->errorFormatter)($this->values, $data));
        }

        return ValidationResult::valid($data);
    }
}
