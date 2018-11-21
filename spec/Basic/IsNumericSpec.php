<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsNumeric;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

describe('IsNumeric', function () {
    $isNumeric = new IsNumeric();

    it('returns a valid result if the argument is numeric', function () use ($isNumeric) {
        expect($isNumeric->validate(12.34)->equals(ValidationResult::valid(12.34)))->toBeTruthy();
    });

    it('returns an error result if the argument is not numeric', function () use ($isNumeric) {
        expect($isNumeric->validate('gigi')->equals(ValidationResult::errors([IsNumeric::MESSAGE])))->toBeTruthy();
    });

    it('returns a custom error result if the argument is not numeric and a custom formatter is passed', function () {
        $isFloat = IsNumeric::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isFloat->validate('gigi')->equals(ValidationResult::errors(['gigi'])))->toBeTruthy();
    });

    it('returns a translated error result if the argument is not numeric and a translator is passed', function () {
        $isFloat = IsNumeric::withTranslator(KeyValueTranslator::withDictionary([
            IsNumeric::MESSAGE => 'NO NUMERIC HERE!'
        ]));

        expect($isFloat->validate('gigi')->equals(ValidationResult::errors(['NO NUMERIC HERE!'])))->toBeTruthy();
    });
});
