<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

final class IsString extends ComposingAssertion implements Validation
{
    public const NOT_A_STRING = 'is-string.not-a-string';

    public function __construct(?callable $errorFormatter = null)
    {
        $this->isAsAsserted = IsAsAsserted::withAssertionAndErrorFormatter(
            'is_string',
            is_callable($errorFormatter) ?
                $errorFormatter :
                function ($data) {
                    return [self::NOT_A_STRING];
                }
        );
    }

    public static function withTranslator(Translator $translator): self
    {
        return self::withTranslatorAndMessage($translator, self::NOT_A_STRING);
    }
}
