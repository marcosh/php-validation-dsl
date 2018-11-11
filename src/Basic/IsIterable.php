<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

final class IsIterable extends ComposingAssertion implements Validation
{
    public const NOT_AN_ITERABLE = 'is-iterable.not-an-iterable';

    public function __construct(?callable $errorFormatter = null)
    {
        $this->isAsAsserted = IsAsAsserted::withAssertionAndErrorFormatter(
            'is_iterable',
            is_callable($errorFormatter) ?
                $errorFormatter :
                function ($data) {
                    return [self::NOT_AN_ITERABLE];
                }
        );
    }

    public static function withTranslator(Translator $translator): self
    {
        return self::withTranslatorAndMessage($translator, self::NOT_AN_ITERABLE);
    }
}
