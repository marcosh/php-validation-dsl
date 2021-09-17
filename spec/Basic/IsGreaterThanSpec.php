<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsGreaterThan;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

describe('IsGreaterThan', function () {
    $isGreaterThan = IsGreaterThan::withBound(42);

    it('returns a valid result if the value is greater than the bound', function () use ($isGreaterThan) {
        expect($isGreaterThan->validate(87)->equals(ValidationResult::valid(87)))->toBeTruthy();
    });

    it('returns an error result if the value is equal to be bound', function () use ($isGreaterThan) {
        expect($isGreaterThan->validate(42)->equals(ValidationResult::errors([IsGreaterThan::MESSAGE])))->toBeTruthy();
    });

    it('returns an error result if the value is less than be bound', function () use ($isGreaterThan) {
        expect($isGreaterThan->validate(23)->equals(ValidationResult::errors([IsGreaterThan::MESSAGE])))->toBeTruthy();
    });

    it(
        'returns a custom error result if the argument is less than the bound and a custom formatter is passed',
        function () {
            $isGreaterThan = IsGreaterThan::withBoundAndFormatter(42, function ($bound, $data) {
                return [$bound . $data];
            });

            expect($isGreaterThan->validate(23)->equals(ValidationResult::errors(['4223'])))->toBeTruthy();
        }
    );

    it(
        'returns a translated error result if the argument is less than the bound and a translator is passed',
        function () {
            $isGreaterThan = IsGreaterThan::withBoundAndTranslator(42, KeyValueTranslator::withDictionary([
                IsGreaterThan::MESSAGE => 'LESS THAN 42!'
            ]));

            expect($isGreaterThan->validate('23')->equals(ValidationResult::errors(['LESS THAN 42!'])))->toBeTruthy();
        }
    );
});
