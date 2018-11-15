<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

final class IsInstanceOf implements Validation
{
    public const NOT_AN_INSTANCE = 'is-instance-of.not-an-instance';

    /**
     * @var string
     */
    private $className;

    /**
     * @var callable $className -> $data -> string[]
     */
    private $errorFormatter;

    private function __construct(string $className, ?callable $errorFormatter = null)
    {
        $this->className = $className;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            function (string $className, $data) {
                return [self::NOT_AN_INSTANCE];
            };
    }

    public static function withClassName(string $className): self
    {
        return new self($className);
    }

    public static function withClassNameAndFormatter(string $className, callable $errorFormatter): self
    {
        return new self($className, $errorFormatter);
    }

    public static function withClassNameAndTranslator(string $className, Translator $translator): self
    {
        return new self(
            $className,
            function (string $className, $data) use ($translator) {
                return [$translator->translate(self::NOT_AN_INSTANCE)];
            }
        );
    }

    public function validate($data, array $context = []): ValidationResult
    {
        if (! $data instanceof $this->className) {
            return ValidationResult::errors(($this->errorFormatter)($this->className, $data));
        }

        return ValidationResult::valid($data);
    }
}
