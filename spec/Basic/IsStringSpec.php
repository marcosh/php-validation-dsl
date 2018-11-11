<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

describe('IsString', function () {
    $isString = new IsString();

    it('returns a valid result if the argument is a string', function () use ($isString) {
        expect($isString->validate('true')->equals(ValidationResult::valid('true')))->toBeTruthy();
    });

    it('returns an error result if the argument is not a string', function () use ($isString) {
        expect($isString->validate(true)->equals(ValidationResult::errors([IsString::NOT_A_STRING])))->toBeTruthy();
    });

    it('returns a custom error result if the argument is not a string and a custom formatter is passed', function () {
        $isString = IsString::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isString->validate(true)->equals(ValidationResult::errors(['1'])))->toBeTruthy();
    });

    it('returns a translated error result if the argument is not a string and a translator is passed', function () {
        $isString = IsString::withTranslator(KeyValueTranslator::withDictionary([
            IsString::NOT_A_STRING => 'NO STRING HERE!'
        ]));

        expect($isString->validate(true)->equals(ValidationResult::errors(['NO STRING HERE!'])))->toBeTruthy();
    });
});
