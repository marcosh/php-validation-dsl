<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsBool;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsBool', function () {
    $isBool = new IsBool();

    it('returns a valid result if the argument is boolean', function () use ($isBool) {
        expect($isBool->validate(true))->toEqual(ValidationResult::valid(true));
    });

    it('returns an error result if the argument is not a boolean', function () use ($isBool) {
        expect($isBool->validate('true'))->toEqual(ValidationResult::errors([IsBool::NOT_A_BOOL]));
    });
});
