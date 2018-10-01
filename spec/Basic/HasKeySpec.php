<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\HasKey;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('HasKey', function () {
    $hasKey = HasKey::withKey('key');

    it('returns a valid result if the key is present', function () use ($hasKey) {
        $data = ['key' => null];

        expect($hasKey->validate($data))->toEqual(ValidationResult::valid($data));
    });

    it('returns an error result if the key is not present', function () use ($hasKey) {
        $data = [];

        expect($hasKey->validate($data))->toEqual(ValidationResult::errors([HasKey::MISSING_KEY]));
    });
});
