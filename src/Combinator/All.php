<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use InvalidArgumentException;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use Webmozart\Assert\Assert;
use function is_callable;

final class All implements Validation
{
    /**
     * @var Validation[]
     */
    private $validations;

    /**
     * @var callable ...array -> array
     */
    private $errorFormatter;

    /**
     * @param Validation[] $validations
     * @param callable|null $errorFormatter
     * @throws InvalidArgumentException
     */
    private function __construct(array $validations, ?callable $errorFormatter = null)
    {
        Assert::allIsInstanceOf($validations, Validation::class);

        $this->validations = $validations;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            'array_merge';
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

    /**
     * @param Validation[] $validations
     * @param callable $errorFormatter
     * @return self
     * @throws InvalidArgumentException
     */
    public static function validationsWithFormatter(array $validations, callable $errorFormatter)
    {
        return new self($validations, $errorFormatter);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        $result = ValidationResult::valid($data);

        foreach ($this->validations as $validation) {
            $result = $result->join(
                $validation->validate($data, $context),
                /**
                 * @template T
                 * @template U
                 * @psalm-param T $a
                 * @psalm-param U $b
                 * @return T
                 */
                function ($a, $b) {
                    return $a;
                },
                $this->errorFormatter
            );
        }

        return $result;
    }
}
