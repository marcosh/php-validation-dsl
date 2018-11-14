<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\NonEmpty;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

describe('NonEmpty', function () {
    $nonEmpty = new NonEmpty();

    it('returns a valid result if the argument is non empty', function () use ($nonEmpty) {
        expect($nonEmpty->validate(42)->equals(ValidationResult::valid(42)))->toBeTruthy();
    });

    it('returns an error result if the argument is empty', function () use ($nonEmpty) {
        expect($nonEmpty->validate([])->equals(ValidationResult::errors([NonEmpty::MESSAGE])))->toBeTruthy();
    });

    it('returns a custom error result if the argument is empty and a custom formatter is passed', function () {
        $isArray = NonEmpty::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isArray->validate(null)->equals(ValidationResult::errors([''])))->toBeTruthy();
    });

    it('returns a translated error message if the argument is empty and a translator is passed', function () {
        $isArray = NonEmpty::withTranslator(KeyValueTranslator::withDictionary([
            NonEmpty::MESSAGE => 'EMPTY HERE!'
        ]));

        expect($isArray->validate([])->equals(ValidationResult::errors(['EMPTY HERE!'])))->toBeTruthy();
    });
});
