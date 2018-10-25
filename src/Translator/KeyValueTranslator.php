<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Translator;

use Webmozart\Assert\Assert;
use function array_key_exists;

final class KeyValueTranslator implements Translator
{
    /**
     * @var array key value dictionary of translations
     */
    private $dictionary;

    private function __construct(array $dictionary)
    {
        Assert::allString(array_keys($dictionary));
        Assert::allString($dictionary);

        $this->dictionary = $dictionary;
    }

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
