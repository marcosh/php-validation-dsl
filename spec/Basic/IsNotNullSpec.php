<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsNotNull;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsNotNull', function () {
    $isNotNull = new IsNotNull();

    it('returns a valid result if the argument is not null', function () use ($isNotNull) {
        expect($isNotNull->validate(42))->toEqual(ValidationResult::valid(42));
    });

    it('returns an error result if the argument is not null', function () use ($isNotNull) {
        expect($isNotNull->validate(null))->toEqual(ValidationResult::errors([IsNotNull::NOT_NOT_NULL]));
    });
});
