<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Translator\Combinator;

use Marcosh\PhpValidationDSL\Translator\Translator;

final class Coalesce implements Translator
{
    /**
     * @var Translator[]
     */
    private $translators;

    private function __construct(array $translators)
    {
        $this->translators = $translators;
    }

    public static function withTranslators(?Translator ...$translators): self
    {
        return new self($translators);
    }

    public function translate(string $string): string
    {
        foreach ($this->translators as $translator) {
            if ($translator instanceof Translator) {
                return $translator->translate($string);
            }
        }

        return $string;
    }
}
