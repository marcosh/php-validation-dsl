<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsBool;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

describe('IsBool', function () {
    $isBool = new IsBool();

    it('returns a valid result if the argument is boolean', function () use ($isBool) {
        expect($isBool->validate(true)->equals(ValidationResult::valid(true)))->toBeTruthy();
    });

    it('returns an error result if the argument is not a boolean', function () use ($isBool) {
        expect($isBool->validate('true')->equals(ValidationResult::errors([IsBool::MESSAGE])))->toBeTruthy();
    });

    it('returns an error result if the argument is not a boolean and a custom formatter is passed', function () {
        $isBool = IsBool::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isBool->validate('true')->equals(ValidationResult::errors(['true'])))->toBeTruthy();
    });

    it('returns a translated error message if the argument is not a boolean and a translator is passed', function () {
        $isArray = IsBool::withTranslator(KeyValueTranslator::withDictionary([
            IsBool::MESSAGE => 'NO BOOL HERE!'
        ]));

        expect($isArray->validate(42)->equals(ValidationResult::errors(['NO BOOL HERE!'])))->toBeTruthy();
    });
});
