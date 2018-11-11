<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

final class IsFloat extends ComposingAssertion implements Validation
{
    public const NOT_A_FLOAT = 'is-float.not-a-float';

    public function __construct(?callable $errorFormatter = null)
    {
        $this->isAsAsserted = IsAsAsserted::withAssertionAndErrorFormatter(
            'is_float',
            is_callable($errorFormatter) ?
                $errorFormatter :
                function ($data) {
                    return [self::NOT_A_FLOAT];
                }
        );
    }

    public static function withTranslator(Translator $translator): self
    {
        return self::withTranslatorAndMessage($translator, self::NOT_A_FLOAT);
    }
}
