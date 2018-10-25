<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Translator;

interface Translator
{
    public function translate(string $string): string;
}
