<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsLessThan;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

describe('IsLessThan', function () {
    $isLessThan = IsLessThan::withBound(42);

    it('returns a valid result if the value is less than the bound', function () use ($isLessThan) {
        expect($isLessThan->validate(23)->equals(ValidationResult::valid(23)))->toBeTruthy();
    });

    it('returns an error result if the value is equal to be bound', function () use ($isLessThan) {
        expect($isLessThan->validate(42)->equals(ValidationResult::errors([IsLessThan::MESSAGE])))->toBeTruthy();
    });

    it('returns an error result if the value is greater than be bound', function () use ($isLessThan) {
        expect($isLessThan->validate(87)->equals(ValidationResult::errors([IsLessThan::MESSAGE])))->toBeTruthy();
    });

    it(
        'returns a custom error result if the argument is greater than the bound and a custom formatter is passed',
        function () {
            $isGreaterThan = IsLessThan::withBoundAndFormatter(42, function ($bound, $data) {
                return [$bound . $data];
            });

            expect($isGreaterThan->validate(87)->equals(ValidationResult::errors(['4287'])))->toBeTruthy();
        }
    );

    it(
        'returns a translated error result if the argument is greater than the bound and a translator is passed',
        function () {
            $isGreaterThan = IsLessThan::withBoundAndTranslator(42, KeyValueTranslator::withDictionary([
                IsLessThan::MESSAGE => 'MORE THAN 42!'
            ]));

            expect($isGreaterThan->validate('87')->equals(ValidationResult::errors(['MORE THAN 42!'])))->toBeTruthy();
        }
    );
});
