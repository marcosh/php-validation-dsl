<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\IsBool;
use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Basic\Regex;
use Marcosh\PhpValidationDSL\Combinator\Sequence;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('Sequence', function () {
    it('returns a valid result in every case if it does not contain any validator', function () {
        $sequence = Sequence::validations([]);

        expect($sequence->validate('gigi'))->toEqual(ValidationResult::valid('gigi'));
    });

    it('returns a valid result if every validator succeeds', function () {
        $sequence = Sequence::validations([
            new IsString(),
            Regex::withPattern('/^[\p{L} ]*$/u')
        ]);

        expect($sequence->validate('gigi'))->toEqual(ValidationResult::valid('gigi'));
    });

    it('returns an error result if one validator fails with just the first error', function () {
        $sequence = Sequence::validations([
            new IsString(),
            new IsBool()
        ]);

        expect($sequence->validate(42))->toEqual(ValidationResult::errors([
            IsString::NOT_A_STRING
        ]));
    });
});
