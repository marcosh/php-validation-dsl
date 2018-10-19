<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use Webmozart\Assert\Assert;

final class Any implements Validation
{
    public const NOT_EVEN_ONE = 'any.not-even-one';

    /**
     * @var Validation[]
     */
    private $validations;

    /**
     * @param Validation[] $validations
     */
    public function __construct(array $validations)
    {
        Assert::allIsInstanceOf($validations, Validation::class);

        $this->validations = $validations;
    }

    /**
     * @param Validation[] $validations
     * @return self
     */
    public static function validations(array $validations): self
    {
        return new self($validations);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        $result = ValidationResult::errors([]);

        foreach ($this->validations as $validation) {
            $result = $result->meet($validation->validate($data, $context), 'array_merge');
        }

        $result = $result->mapErrors(function (array $messages) {
            return [
                self::NOT_EVEN_ONE => $messages
            ];
        });
        return $result;
    }
}
