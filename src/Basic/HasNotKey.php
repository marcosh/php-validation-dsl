<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;

use function is_callable;

/**
 * @template E
 * @template A of array
 * @implements Validation<A, E, A>
 */
final class HasNotKey implements Validation
{
    public const PRESENT_KEY = 'has-not-key.present-key';

    /** @var array-key */
    private $key;

    /** @var callable(array-key, A): E[] */
    private $errorFormatter;

    /**
     * @param array-key $key
     * @param null|callable(array-key, A): E[] $errorFormatter
     */
    private function __construct($key, ?callable $errorFormatter = null)
    {
        $this->key = $key;

        /** @psalm-suppress PossiblyInvalidPropertyAssignmentValue */
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            /**
             * @param array-key $key
             * @param A $data
             * @return string[]
             */
            function (string $key, $data): array {
                return [self::PRESENT_KEY];
            };
    }

    /**
     * @param array-key $key
     */
    public static function withKey(string $key): self
    {
        return new self($key);
    }

    /**
     * @template B of array
     * @param array-key $key
     * @param callable(array-key, B): E[] $errorFormatter
     */
    public static function withKeyAndFormatter(string $key, callable $errorFormatter): self
    {
        return new self($key, $errorFormatter);
    }

    /**
     * @param array-key $key
     * @param Translator $translator
     * @return self<string, array>
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public static function withKeyAndTranslator($key, Translator $translator): self
    {
        return new self(
            $key,
            /**
             * @param array-key $key
             * @param array $data
             * @return string[]
             */
            function (string $key, $data) use ($translator): array {
                return [$translator->translate(self::PRESENT_KEY)];
            }
        );
    }

    /**
     * @param A $data
     * @return ValidationResult<E, A>
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function validate($data, array $context = []): ValidationResult
    {
        if (array_key_exists($this->key, $data)) {
            /** @var ValidationResult<E, A> $ret */
            $ret = ValidationResult::errors(($this->errorFormatter)($this->key, $data));

            return $ret;
        }

        return ValidationResult::valid($data);
    }
}
