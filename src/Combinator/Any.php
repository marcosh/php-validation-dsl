<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use Webmozart\Assert\Assert;
use function is_callable;

final class Any implements Validation
{
    public const NOT_EVEN_ONE = 'any.not-even-one';

    /**
     * @var Validation[]
     */
    private $validations;

    /**
     * @var callable with signature $messages -> array
     */
    private $errorFormatter;

    /**
     * @param Validation[] $validations
     * @param callable|null $errorFormatter
     */
    private function __construct(array $validations, ?callable $errorFormatter = null)
    {
        Assert::allIsInstanceOf($validations, Validation::class);

        $this->validations = $validations;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            /**
             * @return array[]
             *
             * @psalm-return array<string, array>
             */
            function (array $messages): array {
                return [
                    self::NOT_EVEN_ONE => $messages
                ];
            };
    }

    /**
     * @param Validation[] $validations
     * @return self
     */
    public static function validations(array $validations): self
    {
        return new self($validations);
    }

    /**
     * @param Validation[] $validations
     * @param callable $errorFormatter
     * @return self
     */
    public static function validationsWithFormatter(array $validations, callable $errorFormatter): self
    {
        return new self($validations, $errorFormatter);
    }

    public static function validationsWithTranslator(array $validations, Translator $translator): self
    {
        return new self(
            $validations,
            /**
             * @return array[]
             *
             * @psalm-return array<string, array>
             */
            function (array $messages) use ($translator): array {
                return [
                    $translator->translate(self::NOT_EVEN_ONE) => $messages
                ];
            }
        );
    }

    /**
     * @template T
     * @psalm-param T $data
     * @param mixed $data
     * @param array $context
     * @return ValidationResult
     */
    public function validate($data, array $context = []): ValidationResult
    {
        $result = ValidationResult::errors([]);

        foreach ($this->validations as $validation) {
            $result = $result->meet($validation->validate($data, $context), 'array_merge');
        }

        return $result
            ->mapErrors($this->errorFormatter)
            ->map(
                /**
                 * @return T
                 */
                function () use ($data) {
                    return $data;
                }
            );
    }
}
