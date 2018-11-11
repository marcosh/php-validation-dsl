<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsArray;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

describe('IsArray', function () {
    $isArray = new IsArray();

    it('returns a valid result if the argument is an array', function () use ($isArray) {
        expect($isArray->validate([])->equals(ValidationResult::valid([])))->toBeTruthy();
    });

    it('returns an error result if the argument is not an array', function () use ($isArray) {
        expect($isArray->validate(42)->equals(ValidationResult::errors([IsArray::NOT_AN_ARRAY])))->toBeTruthy();
    });

    it('returns a custom error result if the argument is not an array and a custom formatter is passed', function () {
        $isArray = IsArray::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isArray->validate(42)->equals(ValidationResult::errors(['42'])))->toBeTruthy();
    });

    it('returns a translated error message if the argument is not an array and a translator is passed', function () {
        $isArray = IsArray::withTranslator(KeyValueTranslator::withDictionary([
            IsArray::NOT_AN_ARRAY => 'NO ARRAY HERE!'
        ]));

        expect($isArray->validate(42)->equals(ValidationResult::errors(['NO ARRAY HERE!'])))->toBeTruthy();
    });
});
