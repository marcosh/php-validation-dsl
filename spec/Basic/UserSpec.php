<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\HasKey;
use Marcosh\PhpValidationDSL\Basic\IsArray;
use Marcosh\PhpValidationDSL\Basic\IsAsAsserted;
use Marcosh\PhpValidationDSL\Basic\IsInteger;
use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Basic\NonEmpty;
use Marcosh\PhpValidationDSL\Combinator\All;
use Marcosh\PhpValidationDSL\Combinator\Focus;
use Marcosh\PhpValidationDSL\Combinator\Sequence;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('User validation', function () {
    $userValidation = Sequence::validations([
        new IsArray(),
        All::validations([
            Sequence::validations([
                HasKey::withKey('name'),
                Focus::on(
                    function ($data) {
                        return $data['name'];
                    },
                    Sequence::validations([
                        new IsString(),
                        new NonEmpty()
                    ])
                )
            ]),
            Sequence::validations([
                HasKey::withKey('age'),
                Focus::on(
                    function ($data) {
                        return $data['age'];
                    },
                    Sequence::validations([
                        new IsInteger(),
                        IsAsAsserted::withAssertion(function ($data) {
                            return $data >= 0;
                        })
                    ])
                )
            ])
        ])
    ]);

    it('fails if the data is not an array', function () use ($userValidation) {
        expect($userValidation->validate('gigi')->equals(ValidationResult::errors([IsArray::NOT_AN_ARRAY])))
            ->toBeTruthy();
    });

    it('fails if the name field is missing', function () use ($userValidation) {
        expect($userValidation->validate(['age' => 18])->equals(ValidationResult::errors([HasKey::MISSING_KEY])))
            ->toBeTruthy();
    });

    it('fails if the name field is not a string', function () use ($userValidation) {
        expect(
            $userValidation->validate(['name' => 42, 'age' => 42])
                ->equals(ValidationResult::errors([IsString::NOT_A_STRING]))
        )->toBeTruthy();
    });

    it('fails if the name field is an empty string', function () use ($userValidation) {
        expect(
            $userValidation->validate(['name' => '', 'age' => 42])
                ->equals(ValidationResult::errors([NonEmpty::EMPTY]))
        )->toBeTruthy();
    });

    it('fails if the age field is missing', function () use ($userValidation) {
        expect($userValidation->validate(['name' => 'gigi'])->equals(ValidationResult::errors([HasKey::MISSING_KEY])))
            ->toBeTruthy();
    });

    it('fails if the age field is not an integer', function () use ($userValidation) {
        expect(
            $userValidation->validate(['name' => 'gigi', 'age' => 'gigi'])
                ->equals(ValidationResult::errors([IsInteger::NOT_AN_INTEGER]))
        )->toBeTruthy();
    });

    it('fails if the age field is negative', function () use ($userValidation) {
        expect(
            $userValidation->validate(['name' => 'gigi', 'age' => -3])
                ->equals(ValidationResult::errors([IsAsAsserted::NOT_AS_ASSERTED]))
        )->toBeTruthy();
    });

    it(
        'succeeds if the name is a non empty string and the age is a positive integer',
        function () use ($userValidation) {
            expect(
                $userValidation->validate(['name' => 'gigi', 'age' => 42])
                    ->equals(ValidationResult::valid(['name' => 'gigi', 'age' => 42]))
            )->toBeTruthy();
        }
    );
});
