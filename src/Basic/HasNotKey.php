<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use function is_callable;
use Marcosh\PhpValidationDSL\Validation;

final class HasNotKey implements Validation
{
    public const PRESENT_KEY = 'has-not-key.present-key';

    /**
     * @var string
     */
    private $key;

    /**
     * @var callable with signature $key -> $data -> string[]
     */
    private $errorFormatter;

    private function __construct(string $key, ?callable $errorFormatter = null)
    {
        $this->key = $key;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            /**
             * @template T
             * @param string $key
             * @param mixed $data
             * @psalm-param T $data
             * @return string[]
             * @psalm-return array{0:string}
             */
            function (string $key, $data): array {
                return [self::PRESENT_KEY];
            };
    }

    public static function withKey(string $key): self
    {
        return new self($key);
    }

    public static function withKeyAndFormatter(string $key, callable $errorFormatter): self
    {
        return new self($key, $errorFormatter);
    }

    public static function withKeyAndTranslator(string $key, Translator $translator): self
    {
        return new self(
            $key,
            /**
             * @template T
             * @param string $key
             * @param mixed $data
             * @psalm-param T $data
             * @return string[]
             * @psalm-return array{0:string}
             */
            function (string $key, $data) use ($translator): array {
                return [$translator->translate(self::PRESENT_KEY)];
            }
        );
    }

    public function validate($data, array $context = []): ValidationResult
    {
        if (array_key_exists($this->key, $data)) {
            return ValidationResult::errors(($this->errorFormatter)($this->key, $data));
        }

        return ValidationResult::valid($data);
    }
}
