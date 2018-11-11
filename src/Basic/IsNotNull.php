<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

final class IsNotNull extends ComposingAssertion implements Validation
{
    public const NOT_NOT_NULL = 'is-not-null.not-not-null';

    public function __construct(?callable $errorFormatter = null)
    {
        $this->isAsAsserted = IsAsAsserted::withAssertionAndErrorFormatter(
            function ($data) {
                return null !== $data;
            },
            is_callable($errorFormatter) ?
                $errorFormatter :
                function ($data) {
                    return [self::NOT_NOT_NULL];
                }
        );
    }

    public static function withTranslator(Translator $translator): self
    {
        return new self(function ($data) use ($translator) {
            return [$translator->translate(self::NOT_NOT_NULL)];
        });
    }
}
