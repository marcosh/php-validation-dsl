<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use InvalidArgumentException;
use Marcosh\PhpValidationDSL\Basic\HasKey;
use Marcosh\PhpValidationDSL\Basic\IsArray;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use Webmozart\Assert\Assert;

final class Associative implements Validation
{
    /** @var Validation[] */
    private $validations;

    /**
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
     */
    public static function validations(array $validations)
    {
        return new self($validations);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        $wholeValidation = Sequence::validations([
            new IsArray(),
            All::validations(array_map(
                /**
                 * @param array-key $key
                 * @param Validation $validation
                 * @return Validation
                 */
                static function ($key, Validation $validation) {
                    return MapErrors::to(
                        Sequence::validations([
                            HasKey::withKey($key),
                            Focus::on(
                                /**
                                 * @param array $wholeData
                                 * @return mixed
                                 */
                                static function (array $wholeData) use ($key) {
                                    return $wholeData[$key];
                                },
                                $validation
                            )
                        ]),
                        /**
                         * @param array $messages
                         * @return array
                         */
                        static function (array $messages) use ($key): array {
                            return [$key => $messages];
                        }
                    );
                },
                array_keys($this->validations),
                $this->validations
            ))
        ]);

        return $wholeValidation->validate($data, $context);
    }
}
