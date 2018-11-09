<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsNotNull;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsNotNull', function () {
    $isNotNull = new IsNotNull();

    it('returns a valid result if the argument is not null', function () use ($isNotNull) {
        expect($isNotNull->validate(42)->equals(ValidationResult::valid(42)))->toBeTruthy();
    });

    it('returns an error result if the argument is not null', function () use ($isNotNull) {
        expect($isNotNull->validate(null)->equals(ValidationResult::errors([IsNotNull::NOT_NOT_NULL])))->toBeTruthy();
    });

    it('returns a custom error result if the argument is not null and a custom formatter is passed', function () {
        $isNotNull = IsNotNull::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isNotNull->validate(null)->equals(ValidationResult::errors([''])))->toBeTruthy();
    });
});
