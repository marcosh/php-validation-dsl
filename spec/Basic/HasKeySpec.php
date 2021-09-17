<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\HasKey;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

use function json_encode;

describe('HasKey', function () {
    $hasKey = HasKey::withKey('key');

    it('returns a valid result if the key is present', function () use ($hasKey) {
        $data = ['key' => null];

        expect($hasKey->validate($data)->equals(ValidationResult::valid($data)))->toBeTruthy();
    });

    it('returns an error result if the key is not present', function () use ($hasKey) {
        $data = [];

        expect($hasKey->validate($data)->equals(ValidationResult::errors([HasKey::MISSING_KEY])))->toBeTruthy();
    });

    it('returns a custom error result if the key is not present and a custom formatter is passed', function () {
        $hasKey = HasKey::withKeyAndFormatter('key', function ($key, $data) {
            return [$key . json_encode($data)];
        });

        $data = [];

        expect($hasKey->validate($data)->equals(ValidationResult::errors(['key' . json_encode([])])))->toBeTruthy();
    });

    it('returns a translated error message if the key is not present and a translator is passed', function () {
        $hasKey = HasKey::withKeyAndTranslator(
            'key',
            KeyValueTranslator::withDictionary([
                HasKey::MISSING_KEY => 'MISSING KEY!'
            ])
        );

        $data = [];

        expect($hasKey->validate($data)->equals(ValidationResult::errors(['MISSING KEY!'])))->toBeTruthy();
    });
});
