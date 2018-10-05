<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsArray;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsArray', function () {
    $isArray = new IsArray();

    it('returns a valid result if the argument is an array', function () use ($isArray) {
        expect($isArray->validate([]))->toEqual(ValidationResult::valid([]));
    });

    it('returns an error result if the argument is not a boolean', function () use ($isArray) {
        expect($isArray->validate(42))->toEqual(ValidationResult::errors([IsArray::NOT_AN_ARRAY]));
    });
});
