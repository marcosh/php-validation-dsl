<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Translator;

use function array_key_exists;

final class KeyValueTranslator implements Translator
{
    /**
     * @var array<string, string> key value dictionary of translations
     */
    private $dictionary;

    /**
     * @param array<string, string> $dictionary
     */
    private function __construct(array $dictionary)
    {
        $this->dictionary = $dictionary;
    }

    /**
     * @param array<string, string> $dictionary
     */
    public static function withDictionary(array $dictionary): self
    {
        return new self($dictionary);
    }

    public function translate(string $string): string
    {
        return array_key_exists($string, $this->dictionary) ?
            $this->dictionary[$string] :
            $string;
    }
}
