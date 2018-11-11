<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

final class IsArray extends ComposingAssertion implements Validation
{
    public const NOT_AN_ARRAY = 'is-array.no-an-array';

    public function __construct(?callable $errorFormatter = null)
    {
        $this->isAsAsserted = IsAsAsserted::withAssertionAndErrorFormatter(
            'is_array',
            is_callable($errorFormatter) ?
                $errorFormatter :
                function ($data) {
                    return [self::NOT_AN_ARRAY];
                }
        );
    }

    public static function withTranslator(Translator $translator): self
    {
        return self::withTranslatorAndMessage($translator, self::NOT_AN_ARRAY);
    }
}
