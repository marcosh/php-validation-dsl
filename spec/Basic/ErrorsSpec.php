<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\Errors;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

describe('Errors', function () {
    $errors = new Errors();

    it('returns an error result', function () use ($errors) {
        expect($errors->validate('anything')->equals(ValidationResult::errors([Errors::MESSAGE])))->toBeTruthy();
    });

    it('returns a custom error result if a custom formatter is passed', function () {
        $isString = Errors::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isString->validate(true)->equals(ValidationResult::errors(['1'])))->toBeTruthy();
    });

    it('returns a translated error result if a translator is passed', function () {
        $isString = Errors::withTranslator(KeyValueTranslator::withDictionary([
            Errors::MESSAGE => 'NOT VALID!'
        ]));

        expect($isString->validate(true)->equals(ValidationResult::errors(['NOT VALID!'])))->toBeTruthy();
    });
});
