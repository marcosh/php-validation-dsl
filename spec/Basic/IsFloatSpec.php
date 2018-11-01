<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsFloat;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsFloat', function () {
    $isFloat = new IsFloat();

    it('returns a valid result if the argument is float', function () use ($isFloat) {
        expect($isFloat->validate(12.34))->toEqual(ValidationResult::valid(12.34));
    });

    it('returns an error result if the argument is not a float', function () use ($isFloat) {
        expect($isFloat->validate('gigi'))->toEqual(ValidationResult::errors([IsFloat::NOT_A_FLOAT]));
    });

    it('returns a custom error result if the argument is not a float and a custom formatter is passed', function () {
        $isFloat = IsFloat::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isFloat->validate('gigi'))->toEqual(ValidationResult::errors(['gigi']));
    });
});
