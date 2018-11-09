<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsNull;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsNull', function () {
    $isNull = new IsNull();

    it('returns a valid result if the argument is null', function () use ($isNull) {
        expect($isNull->validate(null)->equals(ValidationResult::valid(null)))->toBeTruthy();
    });

    it('returns an error result if the argument is not null', function () use ($isNull) {
        expect($isNull->validate(42)->equals(ValidationResult::errors([IsNull::NOT_NULL])))->toBeTruthy();
    });

    it('returns a custom error result if the argument is null and a custom formatter is passed', function () {
        $isNull = IsNull::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isNull->validate(42)->equals(ValidationResult::errors(['42'])))->toBeTruthy();
    });
});
