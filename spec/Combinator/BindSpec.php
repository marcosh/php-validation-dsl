<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Basic\Regex;
use Marcosh\PhpValidationDSL\Combinator\Bind;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('Bind', function () {
    $f = function (string $s) {
        return Regex::withPattern($s)->validate('abc');
    };

    it('does modify the result of a correct validation', function () use ($f) {
        expect(Bind::to(new IsString(), $f)->validate('/[abc]+/')->equals(ValidationResult::valid('abc')))
            ->toBeTruthy();
    });

    it('does not modify the result of a failed validation', function () use ($f) {
        expect(
            Bind::to(new IsString(), $f)->validate('/[def]+/')->equals(ValidationResult::errors([Regex::MESSAGE]))
        )->toBeTruthy();
    });
});
