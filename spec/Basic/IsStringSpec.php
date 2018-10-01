<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsString', function () {
    $isString = new IsString();

    it('returns a valid result if the argument is a string', function () use ($isString) {
        expect($isString->validate('true'))->toEqual(ValidationResult::valid('true'));
    });

    it('returns an error result if the argument is not a string', function () use ($isString) {
        expect($isString->validate(true))->toEqual(ValidationResult::errors([IsString::NOT_A_STRING]));
    });
});
