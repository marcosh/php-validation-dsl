<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Translator;

final class IdentityTranslator implements Translator
{
    public function translate(string $string): string
    {
        return $string;
    }
}
