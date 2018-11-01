<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsNull;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsNull', function () {
    $isNull = new IsNull();

    it('returns a valid result if the argument is null', function () use ($isNull) {
        expect($isNull->validate(null))->toEqual(ValidationResult::valid(null));
    });

    it('returns an error result if the argument is not null', function () use ($isNull) {
        expect($isNull->validate(42))->toEqual(ValidationResult::errors([IsNull::NOT_NULL]));
    });

    it('returns a custom error result if the argument is null and a custom formatter is passed', function () {
        $isNull = IsNull::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isNull->validate(42))->toEqual(ValidationResult::errors(['42']));
    });
});
