<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsInteger;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsInteger', function () {
    $isInteger = new IsInteger();

    it('returns a valid result if the argument is integer', function () use ($isInteger) {
        expect($isInteger->validate(42)->equals(ValidationResult::valid(42)))->toBeTruthy();
    });

    it('returns an error result if the argument is not an integer', function () use ($isInteger) {
        expect($isInteger->validate('gigi')->equals(ValidationResult::errors([IsInteger::NOT_AN_INTEGER])))
            ->toBeTruthy();
    });

    it('returns a custom error result if the argument is not an int and a custom formatter is passed', function () {
        $isInteger = IsInteger::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isInteger->validate('gigi')->equals(ValidationResult::errors(['gigi'])))->toBeTruthy();
    });
});
