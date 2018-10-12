<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;

final class IsInstanceOf
{
    public const NOT_AN_INSTANCE = 'is-instance-of.not-an-instance';

    /**
     * @var string
     */
    private $className;

    private function __construct(string $className)
    {
        $this->className = $className;
    }

    public static function withClassName(string $className): self
    {
        return new self($className);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        if (! $data instanceof $this->className) {
            return ValidationResult::errors([self::NOT_AN_INSTANCE]);
        }

        return ValidationResult::valid($data);
    }
}
