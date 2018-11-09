<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsArray;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsArray', function () {
    $isArray = new IsArray();

    it('returns a valid result if the argument is an array', function () use ($isArray) {
        expect($isArray->validate([])->equals(ValidationResult::valid([])))->toBeTruthy();
    });

    it('returns an error result if the argument is not a boolean', function () use ($isArray) {
        expect($isArray->validate(42)->equals(ValidationResult::errors([IsArray::NOT_AN_ARRAY])))->toBeTruthy();
    });

    it('returns a custom error result if the key is not a boolean and a custom formatter is passed', function () {
        $isArray = IsArray::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isArray->validate(42)->equals(ValidationResult::errors(['42'])))->toBeTruthy();
    });
});
