<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use InvalidArgumentException;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use Webmozart\Assert\Assert;

final class All implements Validation
{
    /**
     * @var Validation[]
     */
    private $validations;

    /**
     * @param Validation[] $validations
     * @throws InvalidArgumentException
     */
    public function __construct(array $validations)
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
        $result = ValidationResult::valid($data);

        foreach ($this->validations as $validation) {
            $result = $result->join(
                $validation->validate($data, $context),
                function ($a, $b) {
                    return $a;
                },
                'array_merge'
            );
        }

        return $result;
    }
}
