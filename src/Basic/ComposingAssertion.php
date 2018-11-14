<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use function is_callable;

abstract class ComposingAssertion
{
    public const MESSAGE = 'composing-assertion.not-as-asserted';

    /**
     * @var callable $data -> string[]
     */
    private $errorFormatter;

    public function __construct(?callable $errorFormatter = null)
    {
        $this->errorFormatter = $errorFormatter;
    }

    public static function withFormatter(callable $errorFormatter): self
    {
        return new static($errorFormatter);
    }

    public static function withTranslator(Translator $translator): self
    {
        return new static(function ($data) use ($translator) {
            return [$translator->translate(static::MESSAGE)];
        });
    }

    abstract public function validate($data, array $context = []): ValidationResult;

    protected function validateAssertion(callable $assertion, $data, array $context = []): ValidationResult
    {
        return IsAsAsserted::withAssertionAndErrorFormatter(
            $assertion,
            is_callable($this->errorFormatter) ?
                $this->errorFormatter :
                function ($data) {
                    return [static::MESSAGE];
                }
        )->validate($data, $context);
    }
}
