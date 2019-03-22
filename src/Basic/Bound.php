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
     * @var callable with signature $bound -> $data -> string[]
     */
    private $errorFormatter;

    /**
     * @param mixed $bound
     * @param callable $errorFormatter
     */
    private function __construct($bound, callable $errorFormatter)
    {
        $this->bound = $bound;
        $this->errorFormatter = $errorFormatter;
    }

    /**
     * @param mixed $bound
     * @return Bound
     */
    public static function withBound($bound): self
    {
        return self::withBoundAndFormatter(
            $bound,
            function ($bound, $data) {
                return [static::MESSAGE];
            }
        );
    }

    /**
     * @param mixed $bound
     * @param callable $errorFormatter
     * @return Bound
     */
    public static function withBoundAndFormatter($bound, callable $errorFormatter): self
    {
        return new static($bound, $errorFormatter);
    }

    /**
     * @param mixed $bound
     * @param Translator $translator
     * @return Bound
     */
    public static function withBoundAndTranslator($bound, Translator $translator): self
    {
        return self::withBoundAndFormatter(
            $bound,
            function ($bound, $data) use ($translator) {
                return [$translator->translate(static::MESSAGE)];
            }
        );
    }

    /**
     * @param mixed $data
     * @param array $context
     * @return ValidationResult
     */
    abstract public function validate($data, array $context = []): ValidationResult;

    /**
     * @param callable $assertion
     * @param mixed $data
     * @param array $context
     * @return ValidationResult
     */
    protected function validateAssertion(callable $assertion, $data, array $context = []): ValidationResult
    {
        $bound = $this->bound;
        $errorFormatter = $this->errorFormatter;

        return IsAsAsserted::withAssertionAndErrorFormatter(
            function ($data) use ($bound, $assertion) {
                return $assertion($bound, $data);
            },
            function ($data) use ($bound, $errorFormatter) {
                return $errorFormatter($bound, $data);
            }
        )->validate($data, $context);
    }
}
