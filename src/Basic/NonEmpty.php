<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Translator\Translator;
use function is_callable;
use Marcosh\PhpValidationDSL\Validation;

final class NonEmpty extends ComposingAssertion implements Validation
{
    public const EMPTY = 'non-empty.empty';

    public function __construct(?callable $errorFormatter = null)
    {
        $this->isAsAsserted = IsAsAsserted::withAssertionAndErrorFormatter(
            function ($data) {
                return !empty($data);
            },
            is_callable($errorFormatter) ?
                $errorFormatter :
                function ($data) {
                    return [self::EMPTY];
                }
        );
    }

    public static function withTranslator(Translator $translator): self
    {
        return self::withTranslatorAndMessage($translator, self::EMPTY);
    }
}
