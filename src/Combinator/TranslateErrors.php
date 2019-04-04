<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use function is_array;
use function is_string;

final class TranslateErrors implements Validation
{
    /**
     * @var Validation
     */
    private $validation;

    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Validation $validation, Translator $translator)
    {
        $this->validation = $validation;
        $this->translator = $translator;
    }

    public static function validationWithTranslator(Validation $validation, Translator $translator): self
    {
        return new self($validation, $translator);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        return MapErrors::to($this->validation, [$this, 'translateNestedErrors'])->validate($data, $context);
    }

    public function translateNestedErrors(array $messages): array
    {
        $translator = $this->translator;

        return array_map(
            /**
             * @template T
             * @psalm-param T $message
             * @return T
             */
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
