<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

/**
 * @template A
 * @implements Validation<A, A>
 */
final class IsInstanceOf implements Validation
{
    public const NOT_AN_INSTANCE = 'is-instance-of.not-an-instance';

    /**
     * @var class-string
     */
    private $className;

    /**
     * @var callable(class-string, A): string[]
     */
    private $errorFormatter;

    /**
     * @param class-string $className
     * @param null|callable(class-string, A): string[] $errorFormatter
     */
    private function __construct(string $className, ?callable $errorFormatter = null)
    {
        $this->className = $className;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            /**
             * @param class-string $className
             * @param A $data
             * @return string[]
             */
            function (string $className, $data): array {
                return [self::NOT_AN_INSTANCE];
            };
    }

    /**
     * @param class-string $className
     */
    public static function withClassName(string $className): self
    {
        return new self($className);
    }

    /**
     * @template B
     * @param class-string $className
     * @param callable(class-string, B): string[] $errorFormatter
     */
    public static function withClassNameAndFormatter(string $className, callable $errorFormatter): self
    {
        return new self($className, $errorFormatter);
    }

    /**
     * @param class-string $className
     * @param Translator $translator
     */
    public static function withClassNameAndTranslator(string $className, Translator $translator): self
    {
        return new self(
            $className,
            /**
             * @param class-string $className
             * @param A $data
             * @return string[]
             */
            function (string $className, $data) use ($translator): array {
                return [$translator->translate(self::NOT_AN_INSTANCE)];
            }
        );
    }

    /**
     * @param A $data
     * @return ValidationResult<A>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        if (! $data instanceof $this->className) {
            return ValidationResult::errors(($this->errorFormatter)($this->className, $data));
        }

        return ValidationResult::valid($data);
    }
}
