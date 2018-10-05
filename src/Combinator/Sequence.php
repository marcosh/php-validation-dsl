<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use InvalidArgumentException;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use Webmozart\Assert\Assert;

final class Sequence implements Validation
{
    /**
     * @var Validation[]
     */
    private $validations;

    /**
     * Sequence constructor.
     * @param Validation[] $validations
     * @throws InvalidArgumentException
     */
    private function __construct(array $validations)
    {
        Assert::allIsInstanceOf($validations, Validation::class);

        $this->validations = $validations;
    }

    /**
     * @param Validation[] $validations
     * @return self
     * @throws InvalidArgumentException
     */
    public static function validations(array $validations): self
    {
        return new self($validations);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        return array_reduce(
            $this->validations,
            function (ValidationResult $carry, Validation $validation) use ($context) {
                return $carry->process(
                    function ($validData) use ($validation, $context) {
                        return $validation->validate($validData, $context);
                    },
                    function () use ($carry) {
                        return $carry;
                    }
                );
            },
            ValidationResult::valid($data)
        );
    }
}
