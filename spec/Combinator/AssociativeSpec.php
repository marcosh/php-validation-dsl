<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\HasKey;
use Marcosh\PhpValidationDSL\Basic\IsArray;
use Marcosh\PhpValidationDSL\Basic\IsGreaterThan;
use Marcosh\PhpValidationDSL\Basic\IsInteger;
use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Basic\NonEmpty;
use Marcosh\PhpValidationDSL\Combinator\Associative;
use Marcosh\PhpValidationDSL\Combinator\Sequence;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('Associative', function () {
    $validation = Associative::validations([
        'name' => Sequence::validations([
            new IsString(),
            new NonEmpty()
        ]),
        'age' => Sequence::validations([
            new IsInteger(),
            IsGreaterThan::withBound(0)
        ])
    ]);

    it('returns a valid result is the data pass validation', function () use ($validation) {
        $data = [
            'name' => 'gigi',
            'age' => 42
        ];

        expect($validation->validate($data)->equals(ValidationResult::valid($data)))->toBeTruthy();
    });

    it('returns an error result if the data is not an array', function () use ($validation) {
        expect($validation->validate('gigi')->equals(ValidationResult::errors([IsArray::MESSAGE])))->toBeTruthy();
    });

    it('returns an error result if a key is missing', function () use ($validation) {
        $errors = [
            'name' => [HasKey::MISSING_KEY],
            'age' => [HasKey::MISSING_KEY]
        ];

        expect($validation->validate([])->equals(ValidationResult::errors($errors)))->toBeTruthy();
    });

    it('returns an error result if a value fails its own validation', function () use ($validation) {
        $data = [
            'name' => 42,
            'age' => 'gigi'
        ];

        $errors = [
            'name' => [IsString::MESSAGE],
            'age' => [IsInteger::MESSAGE]
        ];

        expect($validation->validate($data)->equals(ValidationResult::errors($errors)))->toBeTruthy();
    });
});
