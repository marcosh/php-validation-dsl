<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\HasNotKey;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

use function json_encode;

describe('HasNotKey', function () {
    $hasNotKey = HasNotKey::withKey('key');

    it('returns a valid result if the key is absent', function () use ($hasNotKey) {
        $data = [];

        expect($hasNotKey->validate($data)->equals(ValidationResult::valid($data)))->toBeTruthy();
    });

    it('returns an error result if the key is present', function () use ($hasNotKey) {
        $data = ['key' => null];

        expect($hasNotKey->validate($data)->equals(ValidationResult::errors([HasNotKey::PRESENT_KEY])))->toBeTruthy();
    });

    it('returns a custom error result if the key is present and a custom formatter is passed', function () {
        $hasKey = HasNotKey::withKeyAndFormatter('key', function ($key, $data) {
            return [$key . json_encode($data)];
        });

        $data = ['key' => null];

        expect($hasKey->validate($data)->equals(ValidationResult::errors(['key' . json_encode($data)])))->toBeTruthy();
    });

    it('returns a translated error message if the key is present and a translator is passed', function () {
        $hasKey = HasNotKey::withKeyAndTranslator(
            'key',
            KeyValueTranslator::withDictionary([
                HasNotKey::PRESENT_KEY => 'PRESENT KEY!'
            ])
        );

        $data = ['key' => null];

        expect($hasKey->validate($data)->equals(ValidationResult::errors(['PRESENT KEY!'])))->toBeTruthy();
    });
});
