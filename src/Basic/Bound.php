<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;

abstract class Bound
{
    public const MESSAGE = 'composing-bound.not-respecting-bound';

    /** @var mixed could be any comparable type */
    protected $bound;

    /**
     * @var callable $bound -> $data -> string[]
     */
    private $errorFormatter;

    private function __construct($bound, callable $errorFormatter)
    {
        $this->bound = $bound;
        $this->errorFormatter = $errorFormatter;
    }

    public static function withBound($bound): self
    {
        return self::withBoundAndFormatter(
            $bound,
            function ($bound, $data) {
                return [static::MESSAGE];
            }
        );
    }

    public static function withBoundAndFormatter($bound, callable $errorFormatter): self
    {
        return new static($bound, $errorFormatter);
    }

    public static function withBoundAndTranslator($bound, Translator $translator): self
    {
        return self::withBoundAndFormatter(
            $bound,
            function ($bound, $data) use ($translator) {
                return [$translator->translate(static::MESSAGE)];
            }
        );
    }

    abstract public function validate($data, array $context = []): ValidationResult;

    protected function validateAssertion(callable $assertion, $data, array $context = []): ValidationResult
    {
        $bound = $this->bound;
        $errorFormatter = $this->errorFormatter;

        return IsAsAsserted::withAssertionAndErrorFormatter(
            function ($data) use ($bound, $assertion) {
                return $assertion($bound, $data);
            },
            is_callable($this->errorFormatter) ?
                function ($data) use ($bound, $errorFormatter) {
                    return $errorFormatter($bound, $data);
                } :
                function ($data) {
                    return [static::MESSAGE];
                }
        )->validate($data, $context);
    }
}
