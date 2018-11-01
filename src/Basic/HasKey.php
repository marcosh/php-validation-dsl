<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function array_key_exists;
use function is_callable;

final class HasKey implements Validation
{
    public const MISSING_KEY = 'has-key.missing-key';

    /**
     * @var string
     */
    private $key;

    /**
     * @var callable $key -> $data -> string[]
     */
    private $errorFormatter;

    private function __construct(string $key, ?callable $errorFormatter = null)
    {
        $this->key = $key;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            function (string $key, $data) {
                return [self::MISSING_KEY];
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

    public function validate($data, array $context = []): ValidationResult
    {
        if (! array_key_exists($this->key, $data)) {
            return ValidationResult::errors(($this->errorFormatter)($this->key, $data));
        }

        return ValidationResult::valid($data);
    }
}
