<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

final class IsCallable extends ComposingAssertion implements Validation
{
    public const NOT_A_CALLABLE = 'is-callable.not-a-callable';

    public function __construct(?callable $errorFormatter = null)
    {
        $this->isAsAsserted = IsAsAsserted::withAssertionAndErrorFormatter(
            'is_callable',
            is_callable($errorFormatter) ?
                $errorFormatter :
                function ($data) {
                    return [self::NOT_A_CALLABLE];
                }
        );
    }
}
