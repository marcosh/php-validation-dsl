<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsBool;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsBool', function () {
    $isBool = new IsBool();

    it('returns a valid result if the argument is boolean', function () use ($isBool) {
        expect($isBool->validate(true)->equals(ValidationResult::valid(true)))->toBeTruthy();
    });

    it('returns an error result if the argument is not a boolean', function () use ($isBool) {
        expect($isBool->validate('true')->equals(ValidationResult::errors([IsBool::NOT_A_BOOL])))->toBeTruthy();
    });

    it('returns an error result if the argument is not a boolean and a custom formatter is passed', function () {
        $isBool = IsBool::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isBool->validate('true')->equals(ValidationResult::errors(['true'])))->toBeTruthy();
    });
});
