<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

final class IsResource extends ComposingAssertion implements Validation
{
    public const NOT_A_RESOURCE = 'is-resource.not-a-resource';

    public function __construct(?callable $errorFormatter = null)
    {
        $this->isAsAsserted = IsAsAsserted::withAssertionAndErrorFormatter(
            'is_resource',
            is_callable($errorFormatter) ?
                $errorFormatter :
                function ($data) {
                    return [self::NOT_A_RESOURCE];
                }
        );
    }

    public static function withTranslator(Translator $translator): self
    {
        return self::withTranslatorAndMessage($translator, self::NOT_A_RESOURCE);
    }
}
