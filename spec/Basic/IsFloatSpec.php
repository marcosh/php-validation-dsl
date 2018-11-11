<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsFloat;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

describe('IsFloat', function () {
    $isFloat = new IsFloat();

    it('returns a valid result if the argument is float', function () use ($isFloat) {
        expect($isFloat->validate(12.34)->equals(ValidationResult::valid(12.34)))->toBeTruthy();
    });

    it('returns an error result if the argument is not a float', function () use ($isFloat) {
        expect($isFloat->validate('gigi')->equals(ValidationResult::errors([IsFloat::NOT_A_FLOAT])))->toBeTruthy();
    });

    it('returns a custom error result if the argument is not a float and a custom formatter is passed', function () {
        $isFloat = IsFloat::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isFloat->validate('gigi')->equals(ValidationResult::errors(['gigi'])))->toBeTruthy();
    });

    it('returns a translated error result if the argument is not a float and a translator is passed', function () {
        $isFloat = IsFloat::withTranslator(KeyValueTranslator::withDictionary([
            IsFloat::NOT_A_FLOAT => 'NO FLOAT HERE!'
        ]));

        expect($isFloat->validate('gigi')->equals(ValidationResult::errors(['NO FLOAT HERE!'])))->toBeTruthy();
    });
});
