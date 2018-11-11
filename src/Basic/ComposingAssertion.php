<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;

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

    protected static function withTranslatorAndMessage(Translator $translator, string $message)
    {
        return new static(function ($data) use ($translator, $message) {
            return [$translator->translate($message)];
        });
    }
}
