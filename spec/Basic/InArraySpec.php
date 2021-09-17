<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\InArray;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

use function json_encode;

describe('InArray', function () {
    $inArray = InArray::withValues(['gigi', 'bepi']);

    it('returns a valid result if the value is present', function () use ($inArray) {
        $data = 'gigi';

        expect($inArray->validate($data)->equals(ValidationResult::valid($data)))->toBeTruthy();
    });

    it('returns an error result if the value is not present', function () use ($inArray) {
        $data = 'toni';

        expect($inArray->validate($data)->equals(ValidationResult::errors([InArray::NOT_IN_ARRAY])))->toBeTruthy();
    });

    it('returns a custom error result if the value is not present and a custom formatter is passed', function () {
        $hasKey = InArray::withValuesAndFormatter(['gigi', 'bepi'], function ($values, $data) {
            return [json_encode($values) . $data];
        });

        $data = 'toni';

        expect($hasKey->validate($data)->equals(ValidationResult::errors([json_encode(['gigi', 'bepi']) . 'toni'])))
            ->toBeTruthy();
    });

    it('returns a translated error message if the value is not present and a translator is passed', function () {
        $hasKey = InArray::withValuesAndTranslator(
            ['gigi', 'bepi'],
            KeyValueTranslator::withDictionary([
                InArray::NOT_IN_ARRAY => 'NOT IN ARRAY!'
            ])
        );

        $data = 'toni';

        expect($hasKey->validate($data)->equals(ValidationResult::errors(['NOT IN ARRAY!'])))->toBeTruthy();
    });
});
