<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;

use function array_key_exists;
use function is_callable;

/**
 * @template E
 * @template A of array
 * @implements Validation<A, E, A>
 */
final class HasKey implements Validation
{
    public const MISSING_KEY = 'has-key.missing-key';

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
            function ($key, array $data): array {
                return [self::MISSING_KEY];
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
     * @template F
     * @param array-key $key
     * @param callable(array-key, array): F[] $errorFormatter
     * @return self<F, array>
     */
    public static function withKeyAndFormatter($key, callable $errorFormatter): self
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
            function ($key, array $data) use ($translator): array {
                return [$translator->translate(self::MISSING_KEY)];
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
        if (! array_key_exists($this->key, $data)) {
            /** @var ValidationResult<E, A> $ret */
            $ret = ValidationResult::errors(($this->errorFormatter)($this->key, $data));

            return $ret;
        }

        return ValidationResult::valid($data);
    }
}
