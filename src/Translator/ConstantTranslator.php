<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Translator;

final class ConstantTranslator implements Translator
{
    /**
     * @var string
     */
    private $translation;

    private function __construct(string $translation)
    {
        $this->translation = $translation;
    }

    public static function withTranslation(string $translation): self
    {
        return new self($translation);
    }

    public function translate(string $string): string
    {
        return $this->translation;
    }
}
