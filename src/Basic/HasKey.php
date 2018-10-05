<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use function array_key_exists;

final class HasKey implements Validation
{
    public const MISSING_KEY = 'has-key.missing-key';

    /**
     * @var string
     */
    private $key;

    private function __construct(string $key)
    {
        $this->key = $key;
    }

    public static function withKey(string $key): self
    {
        return new self($key);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        if (! array_key_exists($this->key, $data)) {
            return ValidationResult::errors([self::MISSING_KEY]);
        }

        return ValidationResult::valid($data);
    }
}
