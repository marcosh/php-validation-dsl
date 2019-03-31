<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Combinator\Apply;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('Apply', function () {
    it('does apply a valid callable to a correct validation', function () {
        $f = ValidationResult::valid(function (string $x) {return strlen($x);});

        expect(Apply::to(new IsString(), $f)->validate('abc')->equals(ValidationResult::valid(3)))->toBeTruthy();
    });

    it('does not modify the result of a failed validation', function () {
        $f = ValidationResult::valid(function (string $x) {return strlen($x);});

        expect(
            Apply::to(new IsString(), $f)->validate(42)->equals(ValidationResult::errors([IsString::MESSAGE]))
        )->toBeTruthy();
    });

    it('does return a failed validation if the applied function is already failed', function () {
        $f = ValidationResult::errors(['gigi']);

        expect(Apply::to(new IsString(), $f)->validate('abc')->equals($f))->toBeTruthy();
    });

    it(
        'does return a failed validation cumulating errors if both the validation and the applied function are failed',
        function () {
            $f = ValidationResult::errors(['gigi']);

            expect(Apply::to(new IsString(), $f)->validate(42)->equals(ValidationResult::errors([
                'gigi',
                IsString::MESSAGE
            ])))->toBeTruthy();
        }
    );
});
