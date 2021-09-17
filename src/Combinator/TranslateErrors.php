<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Closure;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;

use function is_array;
use function is_string;

/**
 * @template A
 * @template E
 * @template B
 * @implements Validation<A, E, B>
 */
final class TranslateErrors implements Validation
{
    /** @var Validation<A, E, B> */
    private $validation;

    /** @var Translator */
    private $translator;

    /**
     * @param Validation<A, E, B> $validation
     */
    public function __construct(Validation $validation, Translator $translator)
    {
        $this->validation = $validation;
        $this->translator = $translator;
    }

    /**
     * @template C
     * @template F
     * @template D
     * @param Validation<C, F, D> $validation
     * @return self<C, F, D>
     */
    public static function validationWithTranslator(Validation $validation, Translator $translator): self
    {
        return new self($validation, $translator);
    }

    /**
     * @param A $data
     * @param array $context
     * @return ValidationResult<E, B>
     */
    public function validate($data, array $context = []): ValidationResult
    {
        return MapErrors::to($this->validation, Closure::fromCallable([$this, 'translateNestedErrors']))->validate($data, $context);
    }

    /**
     * @param E[] $messages
     * @return E[]
     * @psalm-suppress InvalidReturnType
     */
    private function translateNestedErrors(array $messages): array
    {
        $translator = $this->translator;

        /** @psalm-suppress InvalidReturnStatement */
        return array_map(
            function ($message) use ($translator) {
                if (is_string($message)) {
                    return $translator->translate($message);
                }

                if (is_array($message)) {
                    return $this->translateNestedErrors($message);
                }

                return $message;
            },
            $messages
        );
    }
}
